<?php

namespace App\Services;

use App\Jobs\UnitNotifications;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\UserBuildingUnit;

class AssignUnitService
{
    public function unitAssignment_Transaction($user, $unit, $type, $paymentIntentId, $price, int $billing_cycle = 1, $paymentMethod = 'Cash', $currency = 'PKR')
    {
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
                'building_id' => $assignedUnit->building_id,
                'unit_id' => $assignedUnit->unit_id,
                'user_id' => $user->id,
                'organization_id' => $assignedUnit->organization_id,
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

        $transaction = Transaction::create([
            'transaction_title' => $unit->unit_name,
            'transaction_category' => 'New',
            'building_id' => $assignedUnit->building_id,
            'unit_id' => $assignedUnit->unit_id,
            'buyer_id' => $user->id,
            'buyer_type' => 'user',
            'seller_type' => 'organization',
            'seller_id' => $assignedUnit->organization_id,
            'payment_method' => $paymentMethod,
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

        return [$assignedUnit, $transaction];
    }

    public function sendUnitAssignmentNotifications($unit, $transaction, $userId, $assignedUnit, $loggedUser = null)
    {
        $organizationId = $unit->organization_id;
        $price = $transaction->price;
        $type = $assignedUnit->type;
        $actionType = $type === 'Sold' ? 'Purchased' : 'Rented.';
        $durationText = $type === 'Sold' ? '.' : " per {$assignedUnit->billing_cycle} month.";

        $userHeading = "{$unit->unit_name} $actionType Successfully!";
        $userMessage = "Congratulations! You have successfully $actionType {$unit->unit_name} for the price of {$price} PKR{$durationText}";

        $ownerHeading = "$unit->unit_name {$type} successfully";
        $ownerMessage = "$unit->unit_name has been {$type} successfully for Price: PKR $price.";

        $transactionHeading = "Transaction Completed Successfully";
        $transactionMessage = "A payment of PKR $price has been successfully recorded for your unit $unit->name due to $actionType";

        $loggedUserHeading = "Transaction Completed Successfully";
        $loggedUserMessage = "A payment of {$price} PKR has been recorded for {$unit->unit_name}.";

        $userTransactionHeading = "Transaction Successful!";
        $userTransactionMessage = "You have successfully made a payment of {$price} PKR for {$unit->unit_name}.";

        dispatch(new UnitNotifications(
            $organizationId,
            $unit->id,
            $ownerHeading,
            $ownerMessage,
            "owner/units/{$unit->id}/show",

            $loggedUser?->id ?? null,
            "{$unit->unit_name} Assigned Successfully",
            $ownerMessage,
            "owner/units/{$unit->id}/show",

            $userId,
            $userHeading,
            $userMessage,
            "",
        ));

        dispatch(new UnitNotifications(
            $organizationId,
            $unit->id,
            $transactionHeading,
            $transactionMessage,
            "owner/finance/{$transaction->id}/show",

            $loggedUser?->id ?? null,
            $loggedUserHeading,
            $loggedUserMessage,
            "owner/finance/{$transaction->id}/show",

            $userId,
            $userTransactionHeading,
            $userTransactionMessage,
            "",
        ));
    }

}
