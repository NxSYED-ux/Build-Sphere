<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Jobs\MembershipNotifications;
use App\Jobs\UnitNotifications;
use App\Models\BuildingUnit;
use App\Models\Membership;
use App\Models\MembershipUser;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\UserBuildingUnit;
use App\Services\AssignUnitService;
use App\Services\MembershipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;


class CheckOutController extends Controller
{
    // Unit Checkout Functions
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
                DB::rollBack();
                return response()->json([
                    'error' => 'Unit not found. It may have been sold, rented, or its price might have changed.'
                ], 404);
            }

            $organization = $unit->organization;
            $currency = 'PKR';

            try{
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

            } catch (CardException $e) {
                DB::rollBack();
                Log::error('Stripe Error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 402);
            }


            if ($paymentIntent->status !== 'succeeded') {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'error' => 'Payment Failed. Please try again.',
                ], 402);
            }

            $billing_Cycle = 1;
            $type = $unit->sale_or_rent === 'Sale' ? 'Sold' : 'Rented';

            $assignUnitService = new AssignUnitService();
            [$assignedUnit, $transaction] = $assignUnitService->unitAssignment_Transaction($user, $unit, $type, $paymentIntent->id, $request->price, $billing_Cycle, 'Card');
            $assignUnitService->sendUnitAssignmentNotifications(
                $unit,
                $transaction,
                $user->id,
                $assignedUnit
            );

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
            try{
                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            } catch (CardException $e) {
                DB::rollBack();
                Log::error('Stripe Error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 402);
            }


            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment Failed. Please try again.',
                ], 402);
            }

            DB::beginTransaction();

            $unit = BuildingUnit::where('id', $request->unit_id)
                ->where('availability_status', 'Available')
                ->where('sale_or_rent', '!=', 'Not Available')
                ->where('price', $request->price)
                ->lockForUpdate()
                ->first();

            if (!$unit) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Unit not found. It may have been sold, rented, or its price might have changed.'
                ], 404);
            }

            $billing_Cycle = 1;
            $type = $unit->sale_or_rent === 'Sale' ? 'Sold' : 'Rented';

            $assignUnitService = new AssignUnitService();
            [$assignedUnit, $transaction] = $assignUnitService->unitAssignment_Transaction($user, $unit, $type, $paymentIntent->id, $request->price, $billing_Cycle, 'Card');
            $assignUnitService->sendUnitAssignmentNotifications(
                $unit,
                $transaction,
                $user->id,
                $assignedUnit
            );

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


    // Membership Checkout functions
    public function membershipsOnlinePayment(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
            'membership_id' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        try {
            DB::beginTransaction();

            $membershipData = Membership::where('id', $request->membership_id)
                ->where('status', '!=', 'Archived')
                ->where('price', $request->price)
                ->with(['organization'])
                ->first();

            if (!$membershipData) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Membership not found.'
                ], 404);
            }

            $isAlreadySubscribed = MembershipUser::where('user_id', $user->id)
                ->where('membership_id', $membershipData->id)
                ->where('status', 1)
                ->exists();

            if ($isAlreadySubscribed) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Membership is already subscribed.'
                ], 404);
            }

            $organization = $membershipData->organization;

            try{
                Stripe::setApiKey(config('services.stripe.secret'));

                $paymentIntent = PaymentIntent::create([
                    'amount' => $membershipData->price * 100,
                    'currency' => $membershipData->currency,
                    'customer' => $user->customer_payment_id,
                    'payment_method' => $request->payment_method_id,
                    'confirm' => true,
                    'description' => "{$membershipData->name} - {$membershipData->price}",
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never',
                    ],
                    'transfer_data' => [
                        'destination' => $organization->payment_gateway_merchant_id,
                        'amount' => $membershipData->price * 100,
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

            } catch (CardException $e) {
                DB::rollBack();
                Log::error('Stripe Error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 402);
            }


            if ($paymentIntent->status !== 'succeeded') {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'error' => 'Payment Failed. Please try again.',
                ], 402);
            }

            $membershipService = new MembershipService();
            $transaction = $membershipService->membershipAssignment_Transaction($user, $membershipData, $paymentIntent->id, 'Card');
            $membershipService->sendMembershipSuccessNotifications($membershipData, $transaction, $user);

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

    public function completeMembershipPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'membership_id' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $user = $request->user();

        try {
            try{
                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            } catch (CardException $e) {
                Log::error('Stripe Error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 402);
            }


            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment Failed. Please try again.',
                ], 402);
            }

            DB::beginTransaction();

            $membershipData = Membership::where('id', $request->membership_id)
                ->where('status', '!=', 'Archived')
                ->where('price', $request->price)
                ->with(['organization'])
                ->first();

            if (!$membershipData) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Membership not found.'
                ], 404);
            }

            $isAlreadySubscribed = MembershipUser::where('user_id', $user->id)
                ->where('membership_id', $membershipData->id)
                ->where('status', 1)
                ->exists();

            if ($isAlreadySubscribed) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Membership is already subscribed.'
                ], 404);
            }

            $membershipService = new MembershipService();
            $transaction = $membershipService->membershipAssignment_Transaction($user, $membershipData, $paymentIntent->id, 'Card');
            $membershipService->sendMembershipSuccessNotifications($membershipData, $transaction, $user);

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

}
