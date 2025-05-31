<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Plan;
use App\Models\PlanService;
use App\Models\PlanServiceCatalog;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\PlanSubscriptionItem;
use App\Jobs\ProcessSuccessfulCheckout;
use App\Notifications\DatabaseOnlyNotification;
use App\Notifications\EmailNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Refund;

class SubscriptionService
{
    public function handlePlanRenewalOrUpgrade($user, $organization_id, $plan, $planDetails, $planCycle, $paymentIntent, $paymentMethod, bool $notifications = true, bool $renewal = false)
    {
        $services = $planDetails['services'];

        $org_subscription = Subscription::where('organization_id', $organization_id)
            ->where('source_name', 'plan')
            ->latest('created_at')
            ->first();

        if (!$org_subscription) {
            Organization::where('id', $organization_id)->update(['status' => 'Enable']);

            ProcessSuccessfulCheckout::dispatch(
                $user->id,
                $organization_id,
                $plan->id,
                $planDetails,
                $planCycle,
                $paymentIntent->id,
                now(),
                'Card'
            );

            return ['success' => true];
        }

        if($renewal){
            $ends_at = $org_subscription->ends_at > now() ? $org_subscription->ends_at : now();
        }

        DB::beginTransaction();

        try{
            if ($org_subscription->subscription_status === 'Ended'){
                Organization::where('id', $organization_id)->update(['status' => 'Enable']);
            }
            else{
                $org_subscription->update([
                    'subscription_status' => 'Ended',
                    'ends_at' => now(),
                ]);
            }

            $subscription = Subscription::create([
                'customer_payment_id' => $user->customer_payment_id,
                'user_id' => $user->id,
                'organization_id' => $organization_id,
                'source_id' => $plan->id,
                'source_name' => 'plan',
                'billing_cycle' => $planCycle,
                'subscription_status' => 'Active',
                'price_at_subscription' => $planDetails['total_price'],
                'currency_at_subscription' => $plan->currency,
                'ends_at' => $renewal ? $ends_at->copy()->addMonths((int) $planCycle) : now()->addMonths((int) $planCycle),
            ]);

            $transaction = Transaction::create([
                'transaction_title' => "{$plan->name} ({$planCycle} Months)",
                'transaction_category' => $renewal ? 'Renewal' : 'Upgrade',
                'plan_id' => $plan->id,
                'buyer_id' => $organization_id,
                'buyer_type' => 'organization',
                'seller_type' => 'platform',
                'payment_method' => $paymentMethod,
                'gateway_payment_id' => $paymentIntent?->id,
                'price' => $planDetails['total_price'],
                'currency' => $planDetails['currency'],
                'status' => 'Completed',
                'is_subscription' => true,
                'billing_cycle' => $planCycle . ' Months',
                'subscription_start_date' => now(),
                'subscription_end_date' => now()->addMonths((int) $planCycle),
                'source_id' => $subscription->id,
                'source_name' => 'subscription',
            ]);

            $whereServiceIds = array_column($services->toArray(), 'service_catalog_id');

            $existingServices = PlanSubscriptionItem::where('organization_id', $organization_id)
                ->whereIn('service_catalog_id', $whereServiceIds)
                ->get()
                ->keyBy('service_catalog_id');

            $result = $this->updatePlanServices($services, $existingServices, $plan, $organization_id, $paymentIntent, $subscription->id);

            if ($result['error']) {
                DB::rollBack();
                Log::error('Error : ' . $result['error']);
                return ['error' => $result['error']];
            }

            DB::commit();

            if($notifications){
                $this->sendPlanUpgradeNotifications($user, $plan, $transaction);
            }

            return ['success' => true, 'transaction' => $transaction];

        }catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DB Transaction Failed during checkout: ' . $e->getMessage());
            return ['error' => 'Payment succeeded, but something went wrong while saving data. Please contact support.'];
        }
    }

    public function getValidatedPlanWithBillingCycle($planId, $billingCycleId, $statuses = ['Deleted', 'Inactive'])
    {
        return Plan::where('id', $planId)
            ->whereNotIn('status', $statuses)
            ->whereHas('services', function ($query) use ($billingCycleId) {
                $query->with('serviceCatalog')
                    ->whereHas('prices', function ($priceQuery) use ($billingCycleId) {
                        $priceQuery->where('billing_cycle_id', $billingCycleId);
                    });
            })
            ->with(['services' => function ($query) use ($billingCycleId) {
                $query->with('serviceCatalog')
                    ->whereHas('prices', function ($q) use ($billingCycleId) {
                        $q->where('billing_cycle_id', $billingCycleId);
                    })
                    ->with(['prices' => function ($priceQuery) use ($billingCycleId) {
                        $priceQuery->where('billing_cycle_id', $billingCycleId);
                    }]);
            }])
            ->first();
    }

    public function getPlanDetailsWithTotalPrice($plan)
    {
        $totalPrice = 0;
        $services = $plan->services->map(function ($service) use (&$totalPrice) {
            $price = $service->prices->first();
            if ($price) $totalPrice += $price->price;

            return [
                'service_id' => $service->id,
                'service_catalog_id' => $service->serviceCatalog->id,
                'service_name' => $service->serviceCatalog->title ?? '',
                'service_description' => $service->serviceCatalog->description ?? '',
                'service_quantity' => $service->quantity,
                'service_meta' => $service->meta,
                'subscription_id' => $service->subscription_id,
                'price' => $price ? [
                    'price' => $price->price,
                ] : null,
            ];
        });

        return [
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'plan_description' => $plan->description,
            'currency' => $plan->currency,
            'total_price' => $totalPrice,
            'services' => $services,
        ];
    }

    public function handleStripePaymentFailure($e, $plan, $planCycle, $request, $planDetails)
    {
        $error = method_exists($e, 'getError') ? $e->getError() : null;

        Transaction::create([
            'transaction_title' => "{$plan->name} ({$planCycle} Months)",
            'transaction_category' => 'New',
            'buyer_id' => $request->organization_id,
            'buyer_type' => 'organization',
            'seller_type' => 'platform',
            'payment_method' => 'Card',
            'gateway_payment_id' => $e->id ?? null,
            'price' => $planDetails['total_price'],
            'currency' => $planDetails['currency'],
            'status' => 'Failed',
            'source_id' => $plan->id,
            'source_name' => 'plan',
        ]);

        return response()->json([
            'error' => $error->message ?? 'Payment failed.',
            'code' => $error->code ?? 'unknown_error',
            'type' => $error->type ?? 'card_error',
        ], 402);
    }

    public function checkServiceUsageLimit($serviceId, $serviceName, bool $redirect = true)
    {
        $user = request()->user();
        $token = request()->attributes->get('token');
        $organization_id = $token['organization_id'];

        $errorHeading = $user->role_id === 2 ? 'plan_upgrade_error' : 'error';

        $subscriptionLimit = PlanSubscriptionItem::where('organization_id', $organization_id)
            ->where('service_catalog_id', $serviceId)
            ->first();

        if (!$subscriptionLimit) {
            $errorMessage = "The current plan doesn't include {$serviceName}. Please upgrade your plan to access this service.";
            return $redirect ? redirect()->back()->with($errorHeading, $errorMessage) : [ 'success' => false];
        }

        if ($subscriptionLimit->used >= $subscriptionLimit->quantity) {
            $errorMessage = "You have reached the {$serviceName} limit. Please upgrade your plan to add more.";
            return $redirect ? redirect()->back()->with($errorHeading, $errorMessage) : [ 'success' => false];
        }

        return [ 'success' => true, 'subscriptionItem' => $subscriptionLimit ];
    }



    // Helper Functions
    private function updatePlanServices($services, $existingServices, $plan, $organization_id, $paymentIntent, $subscriptionId)
    {
        foreach ($services as $service) {
            $existingService = $existingServices->get($service['service_catalog_id']);

            if ($existingService) {
                $serviceCatalog = PlanServiceCatalog::find($service['service_catalog_id']);
                $parentId = $serviceCatalog?->parent_id;
                $meta = $existingService->meta ?? [];

                if ($parentId) {
                    $parentPlanService = PlanService::where('service_catalog_id', $parentId)
                        ->where('plan_id', $plan->id)
                        ->select('quantity')
                        ->first();

                    if ($parentPlanService) {
                        $meta['quantity'] = $parentPlanService->quantity;
                    }
                }

                if ($service['service_quantity'] >= $existingService->used) {
                    $existingService->update([
                        'quantity' => $service['service_quantity'],
                        'meta' => $meta,
                        'subscription_id' => $subscriptionId,
                    ]);
                } else {
                    $errorMessage = 'Selected Plan has fewer service items than the used service items.';

                    if ($paymentIntent && isset($paymentIntent->charges->data[0]->id)) {
                        try {
                            $chargeId = $paymentIntent->charges->data[0]->id;
                            Refund::create(['charge' => $chargeId]);
                            $errorMessage .= ' Your payment has been refunded automatically. If you do not see the refund within a few minutes, please check with your bank or contact support.';
                        } catch (\Exception $e) {
                            Log::error('Stripe refund failed: ' . $e->getMessage());
                            $errorMessage .= ' Refund attempt failed, please contact support.';
                        }
                    }

                    DB::rollBack();
                    return [
                        'error' => true,
                        'message' => $errorMessage
                    ];
                }
            } else {
                PlanSubscriptionItem::create([
                    'organization_id' => $organization_id,
                    'subscription_id' => $subscriptionId,
                    'service_catalog_id' => $service['service_catalog_id'],
                    'quantity' => $service['service_quantity'],
                    'meta' => $service['service_meta'] ?? null,
                ]);
            }
        }

        return ['error' => false];
    }

    private function sendPlanUpgradeNotifications($user, $plan, $transaction)
    {
        $user->notify(new EmailNotification(
            'uploads/Notification/Light-theme-Logo.svg',
            'Plan Upgraded Successfully!',
            "Dear {$user->name},<br>Your plan has been upgraded successfully! You now have access to additional features and enhanced capabilities. Start exploring your new benefits today through your dashboard.<br>Thank you for choosing us!",
            ['web' => 'owner/dashboard']
        ));

        $user->notify(new DatabaseOnlyNotification(
            'uploads/Notification/Transaction.jpg',
            'Transaction Successful - Plan Upgraded',
            "Great news, {$user->name}! Your payment for the <strong>{$plan->name}</strong> plan has been successfully processed. Your plan is now upgraded, unlocking all the exclusive features and benefits.",
            ['web' => "owner/finance/{$transaction->id}/show"]
        ));
    }

}
