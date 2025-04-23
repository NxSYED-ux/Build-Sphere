<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\BuildingUnit;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\UserBuildingUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;


class CheckOutController extends Controller
{
    public function unitsOnlinePayment(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
            'unit_id' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        try {
            DB::beginTransaction();

            $unit = BuildingUnit::where('id', $request->unit_id)
                ->where('availability_status', 'Available')
                ->where('sale_or_rent', '!=', 'Not Available')
                ->where('price', $request->price)
                ->lockForUpdate()
                ->first();

            if (!$unit) {
                return response()->json([
                    'error' => 'Unit not found. It may have been sold, rented, or its price might have changed.'
                ], 404);
            }

            $organization = $unit->organization;

            $currency = 'PKR';

            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $unit->price * 100,
                'currency' => $currency,
                'customer' => $user->customer_payment_id,
                'payment_method' => $request->payment_method_id,
                'confirm' => true,
                'description' => "{$unit->name} - {$unit->price}",
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'transfer_data' => [
                    'destination' => $organization->payment_gateway_merchant_id,
                    'amount' => $unit->price * 100,
                ],
            ]);

            if (
                $paymentIntent->status === 'requires_action' &&
                $paymentIntent->next_action->type === 'use_stripe_sdk'
            ) {
                DB::rollBack();
                return response()->json([
                    'requires_action' => true,
                    'payment_intent_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                ]);
            }

            if ($paymentIntent->status !== 'succeeded') {
                DB::rollBack();
                $errorMessage = $this->getStripeErrorMessage($paymentIntent);

                return response()->json([
                    'success' => false,
                    'error' => $errorMessage,
                    'status' => $paymentIntent->status,
                ], 402);
            }

            [$assignedUnit, $type] = $this->assignUnitToUser($user, $unit, $request->price);
            $transaction = $this->createTransaction($user, $unit, $type, $paymentIntent->id, $assignedUnit);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment successful.',
                'transaction' => $transaction,
            ], 200);

        } catch (ApiErrorException $e) {
            DB::rollBack();
            Log::error('Stripe Error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment processing error. Please try again.'], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Payment succeeded, but something went wrong while saving data. Please contact support.',
            ], 500);
        }
    }


    public function completeUnitPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'unit_id' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $user = $request->user();

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {

                $errorMessage = $this->getStripeErrorMessage($paymentIntent);

                return response()->json([
                    'success' => false,
                    'error' => $errorMessage,
                    'status' => $paymentIntent->status,
                ], 402);
            }

            DB::beginTransaction();

            $unit = BuildingUnit::where('id', $request->unit_id)
                ->where('availability_status', 'Available')
                ->where('price', $request->price)
                ->lockForUpdate()
                ->first();

            if (!$unit) {
                return response()->json(['error' => 'Unit not available.'], 404);
            }

            [$assignedUnit, $type] = $this->assignUnitToUser($user, $unit, $request->price);
            $transaction = $this->createTransaction($user, $unit, $type, $paymentIntent->id, $assignedUnit);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment successful.',
                'transaction' => $transaction,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payment Finalization Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong after payment.'], 500);
        }
    }

    private function getStripeErrorMessage($paymentIntent)
    {
        $errorMessage = 'Payment failed.';

        if (isset($paymentIntent->last_payment_error)) {
            $errorCode = $paymentIntent->last_payment_error->code ?? null;

            if ($errorCode === 'card_declined') {
                $declineCode = $paymentIntent->last_payment_error->decline_code ?? null;

                if ($declineCode === 'insufficient_funds') {
                    $errorMessage = 'Your card has insufficient funds.';
                } elseif ($declineCode === 'generic_decline') {
                    $errorMessage = 'Your card was declined by the bank.';
                } else {
                    $errorMessage = 'Card declined: ' . str_replace('_', ' ', $declineCode);
                }
            } else {
                $errorMessage = $paymentIntent->last_payment_error->message ?? $errorMessage;
            }
        }

        return $errorMessage;
    }

    private function assignUnitToUser($user, BuildingUnit $unit, $price)
    {
        $type = $unit->sale_or_rent === 'Sale' ? 'Sold' : 'Rented';

        $assignedUnit = UserBuildingUnit::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'type' => $type,
            'price' => $price,
            'rent_start_date' => $type === 'Rented' ? now() : null,
            'rent_end_date' => $type === 'Rented' ? now()->addMonths(1) : null,
            'purchase_date' => $type === 'Sold' ? now() : null,
        ]);

        $unit->update(['availability_status' => $type]);

        return [$assignedUnit, $type];
    }

    private function createTransaction($user, $unit, $type, $paymentIntentId, $assignedUnit, $currency = 'PKR')
    {
        $source_id = $assignedUnit->id;
        $source_name = 'user_building_unit';

        if ($type === 'Rented') {
            $subscription = Subscription::create([
                'customer_payment_id' => $user->customer_payment_id,
                'user_id' => $user->id,
                'organization_id' => $unit->organization_id,
                'source_id' => $assignedUnit->id,
                'source_name' => 'user_building_unit',
                'billing_cycle' => 1,
                'subscription_status' => 'Active',
                'price_at_subscription' => $unit->price,
                'currency_at_subscription' => $currency,
                'ends_at' => now()->addMonths(1),
            ]);

            $source_id = $subscription->id;
            $source_name = 'subscription';
        }

        return Transaction::create([
            'transaction_title' => "{$unit->unit_name} ({$type})",
            'transaction_category' => 'New',
            'buyer_id' => $user->id,
            'buyer_type' => 'user',
            'seller_type' => 'organization',
            'payment_method' => 'Card',
            'gateway_payment_id' => $paymentIntentId,
            'price' => $unit->price,
            'currency' => $currency,
            'status' => 'Completed',
            'is_subscription' => $type === 'Rented',
            'billing_cycle' => $type === 'Rented' ? '1 Months' : null,
            'subscription_start_date' => $assignedUnit->rent_start_date,
            'subscription_end_date' => $assignedUnit->rent_end_date,
            'source_id' => $source_id,
            'source_name' => $source_name,
        ]);
    }

    public function membershipsOnlinePayment(Request $request){

    }
}
