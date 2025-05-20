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
            $transaction = $this->unitAssignment_Transaction($user, $unit, $paymentIntent->id, $request->price, $billing_Cycle);

            DB::commit();

            $this->sendUnitSuccessNotifications($organization->id, $unit, $transaction, $billing_Cycle, $user);

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

            $organization = $unit->organization;

            $billing_Cycle = 1;
            $transaction = $this->unitAssignment_Transaction($user, $unit, $paymentIntent->id, $request->price, $billing_Cycle);

            DB::commit();

            $this->sendUnitSuccessNotifications($organization->id, $unit, $transaction, $billing_Cycle, $user);

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

            $transaction = $this->membershipAssignment_Transaction($user, $membershipData, $paymentIntent->id);

            DB::commit();

            $this->sendMembershipSuccessNotifications($organization->id, $membershipData, $transaction, $user);

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

            $organization = $membershipData->organization;

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

            $transaction = $this->membershipAssignment_Transaction($user, $membershipData, $paymentIntent->id);

            DB::commit();

            $this->sendMembershipSuccessNotifications($organization->id, $membershipData, $transaction, $user);

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


    // Helper functions
    private function unitAssignment_Transaction($user, $unit, $paymentIntentId, $price, $billing_cycle = 1, $currency = 'PKR')
    {
        $type = $unit->sale_or_rent === 'Sale' ? 'Sold' : 'Rented';
        $unit->update(['availability_status' => $type]);

        $assignedUnit = UserBuildingUnit::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'building_id' => $unit->building_id,
            'organization_id' => $unit->organization_id,
            'type' => $type,
            'price' => $price,
            'billing_cycle' => $billing_cycle
        ]);

        $source_id = $assignedUnit->id;
        $source_name = 'unit contract';

        if ($type === 'Rented') {
            $subscription = Subscription::create([
                'customer_payment_id' => $user->customer_payment_id,
                'building_id' => $unit->building_id,
                'unit_id' => $unit->id,
                'user_id' => $user->id,
                'organization_id' => $unit->organization_id,
                'source_id' => $source_id,
                'source_name' => $source_name,
                'billing_cycle' => $billing_cycle,
                'subscription_status' => 'Active',
                'price_at_subscription' => $assignedUnit->price,
                'currency_at_subscription' => $currency,
                'ends_at' => now()->addMonths($billing_cycle),
            ]);

            $source_id = $subscription->id;
            $source_name = 'subscription';

            $assignedUnit->update(['subscription_id' => $subscription->id]);
        }

        return Transaction::create([
            'transaction_title' => $unit->unit_name,
            'transaction_category' => 'New',
            'building_id' => $unit->building_id,
            'unit_id' => $unit->id,
            'buyer_id' => $user->id,
            'buyer_type' => 'user',
            'seller_type' => 'organization',
            'seller_id' => $unit->organization_id,
            'payment_method' => 'Card',
            'gateway_payment_id' => $paymentIntentId,
            'price' => $assignedUnit->price,
            'currency' => $currency,
            'status' => 'Completed',
            'is_subscription' => $type === 'Rented',
            'billing_cycle' => $type === 'Rented' ? "{$billing_cycle} Month" : null,
            'subscription_start_date' => $type === 'Rented' ? now() : null,
            'subscription_end_date' => $type === 'Rented' ? now()->addMonths($billing_cycle) : null,
            'source_id' => $source_id,
            'source_name' => $source_name,
        ]);
    }

    private function sendUnitSuccessNotifications($organizationId, $unit, $transaction, $billing_cycle, $user)
    {
        $userId = $user->id;
        $billingCycle = $billing_cycle ?? 1;
        $isSold = $unit->availability_status === 'Sold';
        $actionType = $isSold ? 'Purchased' : 'Rented';
        $unitName = $unit->unit_name;
        $unitPrice = number_format($unit->price);


        $userHeading1 = "$unitName $actionType Successfully!";
        $userMessage1 = "Congratulations! You have successfully " .
            strtolower($actionType) . " $unitName for the price of PKR $unitPrice" .
            ($isSold ? "." : " per $billingCycle month(s).");

        dispatch(new UnitNotifications(
            $organizationId,
            $unit->id,
            "$unitName {$unit->availability_status} successfully",
            "$unitName has been {$unit->availability_status} successfully for Price: PKR $unitPrice.",
            "owner/units/{$unit->id}/show",

            null,
            '',
            '',
            '',

            $userId,
            $userHeading1,
            $userMessage1,
            ""
        ));


        $userHeading2 = "Transaction Successful!";
        $userMessage2 = "You have successfully made a payment of PKR $unitPrice for $unitName.";

        dispatch(new UnitNotifications(
            $organizationId,
            $unit->id,
            "Transaction Completed Successfully",
            "A payment of PKR $unitPrice has been successfully recorded for your unit $unitName due to $actionType.",
            "owner/finance/{$transaction->id}/show",

            null,
            '',
            '',
            '',

            $userId,
            $userHeading2,
            $userMessage2,
            ""
        ));
    }


    private function membershipAssignment_Transaction($user, $membership, $paymentIntentId)
    {
        $source_id = $membership->id;
        $source_name = 'membership';
        $isSubscription = false;

        if ($membership->status === 'Published') {
            $subscription = Subscription::create([
                'customer_payment_id' => $user->customer_payment_id,
                'building_id' => $membership->building_id,
                'unit_id' => $membership->unit_id,
                'user_id' => $user->id,
                'organization_id' => $membership->organization_id,
                'source_id' => $source_id,
                'source_name' => $source_name,
                'billing_cycle' => $membership->duration_months,
                'subscription_status' => 'Active',
                'price_at_subscription' => $membership->price,
                'currency_at_subscription' => $membership->currency,
                'ends_at' => now()->addMonths($membership->duration_months),
            ]);

            $source_id = $subscription->id;
            $source_name = 'subscription';
            $isSubscription = true;
        }

        MembershipUser::create([
            'user_id' => $user->id,
            'membership_id' => $membership->id,
            'subscription_id' => $isSubscription ? $source_id : null,
            'quantity' => $membership->scans_per_day,
            'used' => $membership->scans_per_day,
        ]);

        return Transaction::create([
            'transaction_title' => "{$membership->name}",
            'transaction_category' => 'New',
            'building_id' => $membership->building_id,
            'unit_id' => $membership->unit_id,
            'buyer_id' => $user->id,
            'buyer_type' => 'user',
            'seller_type' => 'organization',
            'seller_id' => $membership->organization_id,
            'payment_method' => 'Card',
            'gateway_payment_id' => $paymentIntentId,
            'price' => $membership->price,
            'currency' => $membership->currency,
            'status' => 'Completed',
            'is_subscription' => $isSubscription,
            'billing_cycle' => $isSubscription ? "{$membership->duration_months} Month" : null,
            'subscription_start_date' => $isSubscription ? now() : null,
            'subscription_end_date' => $isSubscription ? now()->addMonths($membership->duration_months) : null,
            'source_id' => $source_id,
            'source_name' => $source_name,
        ]);
    }

    private function sendMembershipSuccessNotifications($organizationId, $membership, $transaction, $user)
    {
        $userId = $user->id;
        $billingCycle = $membership->duration_months ?? 1;
        $price = $transaction->price ?? $membership->price;

        $userHeading = "{$membership->name} Purchased Successfully!";
        $userMessage = "Congratulations! You have successfully purchased the {$membership->name} "
            . "for the price of {$price} PKR"
            . ($membership->status === 'Non Renewable' ? '.' : " per {$billingCycle} month(s).");


        $ownerHeading = "{$membership->name} sold successfully";
        $ownerMessage = "{$membership->name} has been sold successfully for Price: {$price}";


        $transactionHeading = "Transaction Completed Successfully";
        $transactionMessage = "A payment of {$price} PKR has been successfully recorded for the sale of {$membership->name}.";

        $userTransactionHeading = "Transaction Successful!";
        $userTransactionMessage = "You have successfully made a payment of {$price} PKR for {$membership->name}.";


        dispatch(new MembershipNotifications(
            $organizationId,
            $membership->id,
            $ownerHeading,
            $ownerMessage,
            "owner/memberships/{$membership->id}/show",

            null,
            '',
            '',
            '',

            $userId,
            $userHeading,
            $userMessage,
            "",
        ));


        dispatch(new MembershipNotifications(
            $organizationId,
            $membership->id,
            $transactionHeading,
            $transactionMessage,
            "owner/finance/{$transaction->id}/show",

            null,
            '',
            '',
            '',

            $userId,
            $userTransactionHeading,
            $userTransactionMessage,
            "",
        ));
    }

}
