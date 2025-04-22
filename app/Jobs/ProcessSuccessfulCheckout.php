<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\PlanSubscriptionItem;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessSuccessfulCheckout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $organizationId;
    protected $planId;
    protected $planDetails;
    protected $planCycle;
    protected $paymentIntentId;
    protected $startDate;
    protected $paymentThrough;

    public function __construct($userId, $organizationId, $planId, $planDetails, $planCycle, $paymentIntentId, $startDate, $paymentThrough)
    {
        $this->userId = $userId;
        $this->organizationId = $organizationId;
        $this->planId = $planId;
        $this->planDetails = $planDetails;
        $this->planCycle = $planCycle;
        $this->paymentIntentId = $paymentIntentId;
        $this->startDate = $startDate;
        $this->paymentThrough = $paymentThrough;
    }

    public function handle()
    {
        try {
            $user = User::find($this->userId);
            $plan = Plan::find($this->planId);
            $endDate = $this->startDate->copy()->addMonths((int) $this->planCycle);

            DB::beginTransaction();

            $subscription = Subscription::create([
                'customer_payment_id' => $user->customer_payment_id,
                'user_id' => $user->id,
                'organization_id' => $this->organizationId,
                'source_id' => $plan->id,
                'source_name' => 'plan',
                'billing_cycle' => $this->planCycle,
                'subscription_status' => 'Active',
                'price_at_subscription' => $this->planDetails['total_price'],
                'currency_at_subscription' => $plan->currency,
                'ends_at' => $endDate,
            ]);

            Transaction::create([
                'transaction_title' => "{$plan->name} ({$this->planCycle} Months)",
                'transaction_category' => 'New',
                'buyer_id' => $this->organizationId,
                'buyer_type' => 'organization',
                'seller_type' => 'platform',
                'payment_method' => $this->paymentThrough,
                'gateway_payment_id' => $this->paymentIntentId,
                'price' => $this->planDetails['total_price'],
                'currency' => $this->planDetails['currency'],
                'status' => 'Completed',
                'is_subscription' => true,
                'billing_cycle' => $this->planCycle . ' Months',
                'subscription_start_date' => $this->startDate,
                'subscription_end_date' => $endDate,
                'source_id' => $subscription->id,
                'source_name' => 'subscription',
            ]);

            foreach ($this->planDetails['services'] as $service) {
                PlanSubscriptionItem::create([
                    'organization_id' => $this->organizationId,
                    'subscription_id' => $subscription->id,
                    'service_catalog_id' => $service['service_catalog_id'],
                    'quantity' => $service['service_quantity'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProcessSuccessfulCheckout Job Failed: ' . $e->getMessage());
        }
    }
}
