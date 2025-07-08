<?php

namespace App\Services;

use App\Jobs\MembershipNotifications;
use App\Models\MembershipUser;
use App\Models\Subscription;
use App\Models\Transaction;

class MembershipService
{
    public function membershipAssignment_Transaction($user, $membership, $paymentIntentId = null, $paymentThrough = 'Cash', $purpose = null, $userMembershipRecord = null, $existingSubscriptionRecord = null)
    {
        $source_id = $membership->id;
        $source_name = 'membership';
        $isSubscription = false;

        if ($purpose){
            $endsAt = $userMembershipRecord->ends_at;
            $newEndsAt = $endsAt && $endsAt->isFuture() ? $endsAt->copy()->addMonths($membership->duration_months) : now()->addMonths($membership->duration_months);
        }

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
                'ends_at' => $newEndsAt ?? now()->addMonths($membership->duration_months),
            ]);

            $source_id = $subscription->id;
            $source_name = 'subscription';
            $isSubscription = true;
        }

        if ($purpose){
            if($existingSubscriptionRecord) {
                $existingSubscriptionRecord->update([
                    'subscription_status' => 'Expired',
                    'ends_at' => now(),
                ]);
            }

            MembershipUser::create([
                'user_id' => $userMembershipRecord->user_id,
                'membership_id' => $userMembershipRecord->membership_id,
                'subscription_id' => $isSubscription ? $source_id : null,
                'quantity' => $membership->scans_per_day,
                'used' => $membership->scans_per_day,
                'ends_at' => $newEndsAt,
            ]);

            $userMembershipRecord->update([
                'ends_at' => now(),
                'status' => 0,
            ]);

        }
        else{
            MembershipUser::create([
                'user_id' => $user->id,
                'membership_id' => $membership->id,
                'subscription_id' => $isSubscription ? $source_id : null,
                'quantity' => $membership->scans_per_day,
                'used' => $membership->scans_per_day,
                'ends_at' => now()->addMonths($membership->duration_months),
            ]);
        }

        $transactionData = [
            'transaction_title' => "{$membership->name}",
            'transaction_category' => $purpose ? 'Renew' : 'New',
            'building_id' => $membership->building_id,
            'unit_id' => $membership->unit_id,
            'membership_id' => $membership->id,
            'buyer_id' => $user->id,
            'buyer_type' => 'user',
            'seller_type' => 'organization',
            'seller_id' => $membership->organization_id,
            'payment_method' => $paymentThrough,
            'gateway_payment_id' => $paymentIntentId,
            'price' => $membership->price,
            'currency' => $membership->currency,
            'status' => 'Completed',
            'is_subscription' => $isSubscription,
            'billing_cycle' => $isSubscription ? "{$membership->duration_months} Month" : null,
            'subscription_start_date' => $isSubscription ? now() : null,
            'subscription_end_date' => $isSubscription ? ($newEndsAt ?? now()->addMonths($membership->duration_months)) : null,
            'source_id' => $source_id,
            'source_name' => $source_name,
        ];

        return Transaction::create($transactionData);
    }

    public function sendMembershipSuccessNotifications($membership, $transaction, $user, $loggedUser = null): void
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
            $membership->organization_id,
            $membership->id,
            $ownerHeading,
            $ownerMessage,
            "owner/memberships/{$membership->id}/show",

            $loggedUser?->id ?? null,
            $ownerHeading,
            $ownerMessage,
            "owner/memberships/{$membership->id}/show",

            $userId,
            $userHeading,
            $userMessage,
            "",
        ));

        dispatch(new MembershipNotifications(
            $membership->organization_id,
            $membership->id,
            $transactionHeading,
            $transactionMessage,
            "owner/finance/{$transaction->id}/show",

            $loggedUser?->id ?? null,
            $transactionHeading,
            $transactionMessage,
            "owner/finance/{$transaction->id}/show",

            $userId,
            $userTransactionHeading,
            $userTransactionMessage,
            "",
        ));
    }

}
