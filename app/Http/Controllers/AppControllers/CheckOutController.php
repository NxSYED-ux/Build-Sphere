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

            $currency = 'PKR';
            $type = $unit->sale_or_rent === 'Sale' ? 'Sold' : 'Rented';

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

            if ($paymentIntent->status === 'succeeded') {
                $assignedUnit = UserBuildingUnit::create([
                    'user_id' => $user->id,
                    'unit_id' => $request->unit_id,
                    'type' => $type,
                    'price' => $request->price,
                    'rent_start_date' => $type === 'Rented' ? now() : null,
                    'rent_end_date' => $type === 'Rented' ? now()->addMonths(1) : null,
                    'purchase_date' => $type === 'Sold' ? now() : null,
                ]);

                $unit->update([
                    'availability_status' => $type,
                ]);

                if ($type === 'Rented') {
                    Subscription::create([
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
                }

                Transaction::create([
                    'transaction_title' => "{$unit->unit_name} ({$type})",
                    'transaction_category' => 'New',
                    'buyer_id' => $user->id,
                    'buyer_type' => 'user',
                    'seller_type' => 'organization',
                    'payment_method' => 'Card',
                    'gateway_payment_id' => $paymentIntent->id,
                    'price' => $unit->price,
                    'currency' => $currency,
                    'status' => 'Completed',
                    'is_subscription' => $type === 'Rented',
                    'billing_cycle' => $type === 'Rented' ? '1 Months' : null,
                    'subscription_start_date' => $assignedUnit->rent_start_date,
                    'subscription_end_date' => $assignedUnit->rent_end_date,
                    'source_id' => $assignedUnit->id,
                    'source_name' => 'user_building_unit',
                ]);

                DB::commit();

                return response()->json(['success' => 'Online payment successful.'], 200);
            }

            DB::rollBack();
            return response()->json([
                'error' => 'Payment failed.',
                'status' => $paymentIntent->status,
            ], 402);

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
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json(['error' => 'Payment not completed.'], 400);
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

            $currency = 'PKR';
            $type = $unit->sale_or_rent === 'Sale' ? 'Sold' : 'Rented';

            $assignedUnit = UserBuildingUnit::create([
                'user_id' => $user->id,
                'unit_id' => $request->unit_id,
                'type' => $type,
                'price' => $request->price,
                'rent_start_date' => $type === 'Rented' ? now() : null,
                'rent_end_date' => $type === 'Rented' ? now()->addMonths(1) : null,
                'purchase_date' => $type === 'Sold' ? now() : null,
            ]);

            $unit->update([
                'availability_status' => $type,
            ]);

            if ($type === 'Rented') {
                Subscription::create([
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
            }

            Transaction::create([
                'transaction_title' => "{$unit->unit_name} ({$type})",
                'transaction_category' => 'New',
                'buyer_id' => $user->id,
                'buyer_type' => 'user',
                'seller_type' => 'organization',
                'payment_method' => 'Card',
                'gateway_payment_id' => $paymentIntent->id,
                'price' => $unit->price,
                'currency' => $currency,
                'status' => 'Completed',
                'is_subscription' => $type === 'Rented',
                'billing_cycle' => $type === 'Rented' ? '1 Months' : null,
                'subscription_start_date' => $assignedUnit->rent_start_date,
                'subscription_end_date' => $assignedUnit->rent_end_date,
                'source_id' => $assignedUnit->id,
                'source_name' => 'user_building_unit',
            ]);

            DB::commit();

            return response()->json(['success' => 'Payment completed and unit assigned.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payment Finalization Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong after payment.'], 500);
        }
    }



    public function membershipsOnlinePayment(Request $request){

    }

}
