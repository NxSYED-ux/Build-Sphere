<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSuccessfulCheckout;
use App\Models\BillingCycle;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;


class UpgradePlanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return response()->json(['error' => "Can't access this page, unless you are an organization owner."]);
            }

            $organization_id = $token['organization_id'];

            $current_plan = $this->current_plan($organization_id);

            $org_services = $current_plan['services'];
            $activePlanId = $current_plan['source_id'];
            $activeCycle = $current_plan['billing_cycle'];
            $planCycles = BillingCycle::pluck('duration_months');

            Log::error('Org Services : ' , $org_services);

            return view('Heights.Owner.Plan.upgrade', compact(
                'org_services',
                'planCycles',
                'activePlanId',
                'activeCycle'
            ));
        } catch (\Exception $e) {
            Log::error('Error in Upgrade Plan index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function checkout(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');

        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
            'plan_cycle' => 'required|integer',
            'payment_method_id' => 'required|string',
        ]);

        try {
            $plan = $this->getValidatedPlanWithBillingCycle($request->plan_id, $request->plan_cycle_id);

            if (!$plan) {
                return response()->json(['error' => 'The requested plan is currently unavailable due to administrative changes.'], 404);
            }

            $planDetails = $this->getPlanDetailsWithTotalPrice($plan);

            try{
                Stripe::setApiKey(config('services.stripe.secret'));

                $paymentIntent = PaymentIntent::create([
                    'amount' => $planDetails['total_price'] * 100,
                    'currency' => $planDetails['currency'],
                    'customer' => $user->customer_payment_id,
                    'payment_method' => $request->payment_method_id,
                    'confirm' => true,
                    'description' => $planDetails['plan_name'] . ': ' . $planDetails['plan_description'],
                    'setup_future_usage' => 'off_session',
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never',
                    ],
                ]);

                if (
                    $paymentIntent->status === 'requires_action' &&
                    $paymentIntent->next_action->type === 'use_stripe_sdk'
                ) {
                    return response()->json([
                        'requires_action' => true,
                        'payment_intent_id' => $paymentIntent->id,
                        'client_secret' => $paymentIntent->client_secret,
                    ]);
                }

            } catch (CardException $e) {
                Log::error('Stripe Error: ' . $e->getMessage());
                return $this->handleStripePaymentFailure($e, $plan, $request->plan_cycle, $request, $planDetails);
            }

            if ($paymentIntent->status !== 'succeeded') {
                return $this->handleStripePaymentFailure($paymentIntent, $plan, $request->plan_cycle, $request, $planDetails);
            }

            Customer::update($user->customer_payment_id, [
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method_id,
                ],
            ]);

            try {
                Organization::where('id', $request->organization_id)->update(['status' => 'Enable']);

                ProcessSuccessfulCheckout::dispatch(
                    $user->id,
                    $request->organization_id,
                    $plan->id,
                    $planDetails,
                    $request->plan_cycle,
                    $paymentIntent->id,
                    now(),
                    'Card'
                );

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                Log::error('DB Transaction Failed during checkout: ' . $e->getMessage());

                return response()->json([
                    'error' => 'Payment succeeded, but something went wrong while saving data. Please contact support.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error while Checkout Plan: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkoutComplete(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'owner_id' => 'required|exists:users,id',
            'organization_id' => 'required|exists:organizations,id',
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle' => 'required|integer',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
        ]);

        try {
            $user = User::find($request->owner_id);
            $plan = $this->getValidatedPlanWithBillingCycle($request->plan_id, $request->plan_cycle_id);
            if (!$plan) {
                return response()->json(['error' => 'The requested plan is currently unavailable due to administrative changes.'], 404);
            }

            $planDetails = $this->getPlanDetailsWithTotalPrice($plan);

            try{
                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            } catch (CardException $e) {
                Log::error('Stripe Error: ' . $e->getMessage());
                return $this->handleStripePaymentFailure($e, $plan, $request->plan_cycle, $request, $planDetails);
            }

            if ($paymentIntent->status !== 'succeeded') {
                return $this->handleStripePaymentFailure($paymentIntent, $plan, $request->plan_cycle, $request, $planDetails);
            }

            $paymentMethodId = $paymentIntent->payment_method;

            Customer::update($user->customer_payment_id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);

            $planDetails = $this->getPlanDetailsWithTotalPrice($plan);
            Organization::where('id', $request->organization_id)->update(['status' => 'Enable']);

            ProcessSuccessfulCheckout::dispatch(
                $user->id,
                $request->organization_id,
                $plan->id,
                $planDetails,
                $request->plan_cycle,
                $paymentIntent->id,
                now(),
                'Card'
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error while Completing Checkout Plan: ' . $e->getMessage());

            return response()->json([
                'error' => 'Something went wrong during the final confirmation.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // Helper functions
    private function getValidatedPlanWithBillingCycle($planId, $billingCycleId)
    {
        return Plan::where('id', $planId)
            ->whereNotIn('status', ['Deleted', 'Inactive'])
            ->whereHas('services', function ($query) use ($billingCycleId) {
                $query->whereHas('prices', function ($priceQuery) use ($billingCycleId) {
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

    private function getPlanDetailsWithTotalPrice($plan)
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
            ];
        });

        return [
            'plan_name' => $plan->name,
            'plan_description' => $plan->description,
            'currency' => $plan->currency,
            'total_price' => $totalPrice,
            'services' => $services,
        ];
    }

    private function handleStripePaymentFailure($e, $plan, $planCycle, $request, $planDetails)
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

    private function current_plan(string $organization_id)
    {
        try {
            $subscription = Subscription::where('organization_id', $organization_id)
                ->where('source_name', 'plan')
                ->with([
                    'source',
                    'planSubscriptionItems:id,subscription_id,service_catalog_id,quantity,used',
                    'planSubscriptionItems.serviceCatalog:id,title,icon'
                ])
                ->first();

            $totalUsed = 0;
            $totalQuantity = 0;
            $formatted = null;
            $source_id = null;
            $billing_cycle = null;
            $services = [];

            if ($subscription) {
                $formatted = [
                    'id' => $subscription->id,
                    'name' => $subscription->source->name ?? null,
                    'billing_cycle' => $subscription->billing_cycle,
                    'status' => $subscription->subscription_status,
                    'price' => $subscription->price_at_subscription,
                    'currency' => $subscription->currency_at_subscription,
                    'starts_at' => $subscription->created_at,
                    'ends_at' => $subscription->ends_at,
                    'services' => $subscription->planSubscriptionItems->map(function ($item) use (&$totalUsed, &$totalQuantity) {
                        $totalUsed += $item->used;
                        $totalQuantity += $item->quantity;

                        return [
                            'service_id' => $item->id,
                            'service_catalog_id' => $item->serviceCatalog->id ?? null,
                            'title' => $item->serviceCatalog->title ?? null,
                            'icon' => $item->serviceCatalog->icon ?? null,
                            'quantity' => $item->quantity,
                            'used' => $item->used,
                            'used_percentage' => ($item->quantity > 0) ? number_format(($item->used / $item->quantity) * 100, 2) : 0,
                        ];
                    })->toArray(),
                ];

                $source_id = $subscription->source_id;
                $billing_cycle = $subscription->billing_cycle;
                $services = $subscription->planSubscriptionItems->toArray();
            }

            $overallUsedPercentage = ($totalQuantity > 0) ? number_format(($totalUsed / $totalQuantity) * 100, 2) : 0;

            return [
                'subscription' => $formatted,
                'usage' => $overallUsedPercentage,
                'source_id' => $source_id,
                'billing_cycle' => $billing_cycle,
                'services' => $services,
            ];
        } catch (\Exception $e) {
            Log::error('Error occurred while fetching current plan for organization ID ' . $organization_id . ': ' . $e->getMessage());
            return [
                'subscription' => null,
                'usage' => 0,
                'source_id' => null,
                'billing_cycle' => null,
                'services' => [],
            ];
        }
    }

}
