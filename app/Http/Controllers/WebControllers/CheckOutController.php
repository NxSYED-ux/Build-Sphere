<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSuccessfulCheckout;
use App\Jobs\SendRoleNotification;
use App\Models\BillingCycle;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\PlanService;
use App\Models\PlanServiceCatalog;
use App\Models\PlanSubscriptionItem;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\DatabaseOnlyNotification;
use App\Notifications\EmailNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Stripe;

class CheckOutController extends Controller
{
    public function checkoutIndex(Request $request)
    {
        $request->validate([
            'organization_name' => 'required|string',
        ]);

        try {
            $organization = Organization::where('name', $request->organization_name)
                ->first();

            if (!$organization) {
                return redirect()->back()->with('error', 'Invalid request. Please try again or contact support if the issue persists.');
            }

            $owner_id = $organization->owner_id;
            $organization_id = $organization->id;
            $organization_name = $request->input('organization_name');
            $selectedPackage = $request->input('package');
            $selectedCycle = $request->input('cycle');
            $planCycles = BillingCycle::pluck('duration_months');

            return view('landing-views.checkout', compact(
                'planCycles',
                'owner_id',
                'organization_id',
                'organization_name',
                'selectedPackage',
                'selectedCycle'
            ));
        } catch (\Exception $e) {
            Log::error('Error fetching billing cycles: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }


    public function updatePlanAdminIndex(string $id)
    {
        try {
            $organization = Organization::find($id);

            if(!$organization) {
                return redirect()->back()->with('error', 'Invalid organization');
            }

            $subscription = Subscription::where('organization_id', $organization->id)
                ->where('source_name', 'plan')
                ->first();

            $organization_id = $organization->id;
            $activePlanId = $subscription?->source_id;
            $activeCycle = $subscription?->billing_cycle;
            $planCycles = BillingCycle::pluck('duration_months');

            return view('Heights.Admin.Plan.upgrade', compact(
                'planCycles',
                'activePlanId',
                'activeCycle',
                'organization_id'
            ));
        } catch (\Exception $e) {
            Log::error('Error in Upgrade Plan Admin index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function updatePlanOwnerIndex(Request $request)
    {
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return response()->json(['error' => "Can't access this page, unless you are an organization owner."]);
            }

            $organization_id = $token['organization_id'];

            $subscription = Subscription::where('organization_id', $organization_id)
                ->where('source_name', 'plan')
                ->first();

            $activePlanId = $subscription?->source_id;
            $activeCycle = $subscription?->billing_cycle;
            $planCycles = BillingCycle::pluck('duration_months');

            return view('Heights.Owner.Plan.upgrade', compact(
                'planCycles',
                'activePlanId',
                'activeCycle'
            ));
        } catch (\Exception $e) {
            Log::error('Error in Upgrade Plan owner index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }


    // Checkout
    public function createCheckOut(Request $request)
    {
        $request->validate([
            'owner_id' => 'required',
            'organization_id' => 'required',
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
            'plan_cycle' => 'required|integer',
            'payment_method_id' => 'required|string',
        ]);

        try{
            $user = User::find($request->owner_id);
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            $organization = Organization::where('id', $request->organization_id)
                ->where('status', '!=', 'Enable')
                ->first();

            if (!$organization) {
                return response()->json(['error' => 'An active subscription already exists for your organization.'], 409);
            }

            return $this->checkout($request, $user, 'create', $request->organization_id);
        }catch (\Exception $e) {
            Log::error('Error creating checkout: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    public function updateCheckOut(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
            'plan_cycle' => 'required|integer',
            'payment_method_id' => 'required|string',
        ]);

        try{
            $user = $request->user() ?? abort(403, 'Unauthorized action.');
            $token = $request->attributes->get('token');

            if (empty($token['organization_id'])) {
                return response()->json(['error' => "Can't access this page, unless you are an organization owner."]);
            }

            $organization = Organization::find($token['organization_id']);
            if (!$organization || $organization->owner_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            return $this->checkout($request, $user, 'update', $organization->id);
        }catch (\Exception $e) {
            Log::error('Error creating checkout: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }


    // Checkout Complete
    public function createCheckoutComplete(Request $request)
    {
        $request->validate([
            'owner_id' => 'required',
            'payment_intent_id' => 'required|string',
            'organization_id' => 'required',
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
            'plan_cycle' => 'required|integer',
        ]);

        try{
            $user = User::find($request->owner_id);
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            return $this->checkout($request, $user, 'create', $request->organization_id, true);
        }catch (\Exception $e) {
            Log::error('Error creating complete checkout: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    public function updateCheckoutComplete(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
            'plan_cycle' => 'required|integer',
        ]);

        try{
            $user = $request->user() ?? abort(403, 'Unauthorized action.');
            $token = $request->attributes->get('token');

            if (empty($token['organization_id'])) {
                return response()->json(['error' => "Can't access this page, unless you are an organization owner."]);
            }

            $organization = Organization::find($token['organization_id']);
            if (!$organization || $organization->owner_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            return $this->checkout($request, $user, 'update', $organization->id, true);
        }catch (\Exception $e) {
            Log::error('Error creating checkout: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }


    // Main Checkout Function
    private function checkout(Request $request, $user, string $type, $organization_id, bool $complete = false)
    {
        try {
            $plan = $this->getValidatedPlanWithBillingCycle($request->plan_id, $request->plan_cycle_id);
            if (!$plan) {
                return response()->json(['error' => 'The requested plan is currently unavailable due to administrative changes.'], 404);
            }

            $planDetails = $this->getPlanDetailsWithTotalPrice($plan);

            try {
                Stripe::setApiKey(config('services.stripe.secret'));

                if (!$complete) {
                    try {
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
                    } catch (\Exception $e) {
                        Log::error('Stripe PaymentIntent creation failed: ' . $e->getMessage());
                        return response()->json([
                            'error' => 'Unable to process your payment. Please try again or use a different payment method.',
                        ], 400);
                    }

                    if ($paymentIntent->status === 'requires_action' && $paymentIntent->next_action->type === 'use_stripe_sdk') {
                        return response()->json([
                            'requires_action' => true,
                            'payment_intent_id' => $paymentIntent->id,
                            'client_secret' => $paymentIntent->client_secret,
                        ]);
                    }
                } else {
                    $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

                    if (!$paymentIntent || $paymentIntent->status !== 'succeeded') {
                        return response()->json(['error' => 'Payment was not successful or intent not found.'], 400);
                    }
                }
            } catch (CardException $e) {
                Log::error('Stripe CardException: ' . $e->getMessage());
                return $this->handleStripePaymentFailure($e, $plan, $request->plan_cycle, $request, $planDetails);
            } catch (Exception $e) {
                Log::error('General Stripe Error: ' . $e->getMessage());
                return response()->json([
                    'error' => 'Something went wrong with your payment. Please try again later.',
                ], 500);
            }

            if ($paymentIntent->status === 'requires_payment_method') {
                return response()->json(['error' => 'Payment failed. Please try another payment method.'], 400);
            }

            if ($paymentIntent->status !== 'succeeded') {
                return $this->handleStripePaymentFailure($paymentIntent, $plan, $request->plan_cycle, $request, $planDetails);
            }

            if ($type === 'create') {
                Customer::update($user->customer_payment_id, [
                    'invoice_settings' => [
                        'default_payment_method' => $request->payment_method_id,
                    ],
                ]);
            }

            try {
                if ($type === 'create') {
                    Organization::where('id', $organization_id)->update(['status' => 'Enable']);

                    ProcessSuccessfulCheckout::dispatch(
                        $user->id,
                        $organization_id,
                        $plan->id,
                        $planDetails,
                        $request->plan_cycle,
                        $paymentIntent->id,
                        now(),
                        'Card'
                    );
                } else {
                    $services = $planDetails['services'];
                    $org_subscription = Subscription::where('organization_id', $organization_id)
                        ->where('source_name', 'plan')->first();

                    if(!$org_subscription){
                        Organization::where('id', $organization_id)->update(['status' => 'Enable']);

                        ProcessSuccessfulCheckout::dispatch(
                            $user->id,
                            $organization_id,
                            $plan->id,
                            $planDetails,
                            $request->plan_cycle,
                            $paymentIntent->id,
                            now(),
                            'Card'
                        );

                        return response()->json(['success' => true]);
                    }


                    DB::beginTransaction();


                    $org_subscription->update([
                        'source_id' => $plan->id,
                        'billing_cycle' => $request->plan_cycle,
                        'subscription_status' => 'Active',
                        'price_at_subscription' => $planDetails['total_price'],
                        'currency_at_subscription' => $plan->currency,
                        'ends_at' => now()->addMonths((int) $request->plan_cycle),
                    ]);

                    $transaction = Transaction::create([
                        'transaction_title' => "{$plan->name} ({$request->plan_cycle} Months)",
                        'transaction_category' => 'Renewal',
                        'buyer_id' => $organization_id,
                        'buyer_type' => 'organization',
                        'seller_type' => 'platform',
                        'payment_method' => 'Card',
                        'gateway_payment_id' => $paymentIntent->id,
                        'price' => $planDetails['total_price'],
                        'currency' => $planDetails['currency'],
                        'status' => 'Completed',
                        'is_subscription' => true,
                        'billing_cycle' => $request->plan_cycle . ' Months',
                        'subscription_start_date' => now(),
                        'subscription_end_date' => now()->addMonths((int) $request->plan_cycle),
                        'source_id' => $org_subscription->id,
                        'source_name' => 'subscription',
                    ]);

                    $whereServiceIds = array_column($services->toArray(), 'service_catalog_id');

                    $existingServices = PlanSubscriptionItem::where('organization_id', $organization_id)
                        ->whereIn('service_catalog_id', $whereServiceIds)
                        ->get()
                        ->keyBy('service_catalog_id');


                    $result = $this->updatePlanServices($services, $existingServices, $plan, $organization_id, $paymentIntent);
                    if ($result['error']) {
                        DB::rollBack();
                        Log::error('Error : ' . $result['error']);
                        return response()->json(['error' => $result['message']], 400);
                    }

                    DB::commit();

                    $this->sendPlanUpgradeNotifications($user, $plan, $transaction);
                }

                return response()->json(['success' => true]);

            }catch (\Throwable $e) {
                DB::rollBack();
                Log::error('DB Transaction Failed during checkout: ' . $e->getMessage());

                return response()->json([
                    'error' => 'Payment succeeded, but something went wrong while saving data. Please contact support.',
                ], 500);
            }
        } catch (Exception $e) {
            Log::error('Error while Checkout Plan: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Upgrade Plan Admin
    public function adminUpgradePlan(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');

        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
            'plan_cycle' => 'required|integer',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        try {
            $organization = Organization::findOrFail($request->organization_id);
            $owner = $organization->owner;

            $plan = $this->getValidatedPlanWithBillingCycle($request->plan_id, $request->plan_cycle_id);
            if (!$plan) {
                return redirect()->back()->withInput()->with('error', 'The requested plan is currently unavailable due to administrative changes.');
            }

            $planDetails = $this->getPlanDetailsWithTotalPrice($plan);
            $services = $planDetails['services'];

            $org_subscription = Subscription::where('organization_id', $organization->id)
                ->where('source_name', 'plan')->first();

            if (!$org_subscription) {
                return redirect()->back()->withInput()->with('error', 'No organization subscription found for the selected organization.');
            }

            DB::beginTransaction();

            $startDate = now();
            $endDate = $startDate->copy()->addMonths($request->plan_cycle);

            $org_subscription->update([
                'billing_cycle' => $request->plan_cycle,
                'subscription_status' => 'Active',
                'price_at_subscription' => $planDetails['total_price'],
                'currency_at_subscription' => $plan->currency,
                'ends_at' => $endDate,
            ]);

            $transaction = Transaction::create([
                'transaction_title' => "{$plan->name} ({$request->plan_cycle} Months)",
                'transaction_category' => 'Renewal',
                'buyer_id' => $organization->id,
                'buyer_type' => 'organization',
                'seller_type' => 'platform',
                'payment_method' => 'Cash',
                'gateway_payment_id' => null,
                'price' => $planDetails['total_price'],
                'currency' => $planDetails['currency'],
                'status' => 'Completed',
                'is_subscription' => true,
                'billing_cycle' => $request->plan_cycle . ' Months',
                'subscription_start_date' => $startDate,
                'subscription_end_date' => $endDate,
                'source_id' => $org_subscription->id,
                'source_name' => 'subscription',
            ]);

            $whereServiceIds = array_column($services->toArray(), 'service_catalog_id');

            $existingServices = PlanSubscriptionItem::where('organization_id', $organization->id)
                ->whereIn('service_catalog_id', $whereServiceIds)
                ->get()
                ->keyBy('service_catalog_id');

            $result = $this->updatePlanServices($services, $existingServices, $plan, $organization->id, null);
            if ($result['error']) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', $result['message']);
            }

            DB::commit();

            $this->sendPlanUpgradeNotifications($owner, $plan, $transaction);

            $logo = 'uploads/Notification/Light-theme-Logo.svg';

            dispatch(new SendRoleNotification(
                1,
                $logo,
                "Organization Plan Upgraded",
                "{$organization->name} upgraded to {$plan->name} ({$request->plan_cycle} Months) by {$user->name}.",
                ['web' => "admin/organizations/{$organization->id}/show"],

                $user->id,
                "Plan Upgraded for {$organization->name}",
                "{$organization->name} has successfully upgraded to the {$plan->name} plan ({$request->plan_cycle} Months).",
                ['web' => "admin/organizations/{$organization->id}/show"],
            ));

            return redirect()->route('organizations.show', $organization->id)->with('success', 'Organization plan upgraded successfully.');

        }catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in plan Upgrade Admin: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again later.');
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
                'subscription_id' => $service->subscription_id,
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

    private function updatePlanServices($services, $existingServices, $plan, $organization_id, $paymentIntent)
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
                    'subscription_id' => $service['subscription_id'],
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
            '🎉 Plan Upgraded Successfully!',
            "Dear {$user->name},<br><br>Your plan has been upgraded successfully! You now have access to additional features and enhanced capabilities. Start exploring your new benefits today through your dashboard.<br><br>Thank you for choosing us!",
            ['web' => 'owner/dashboard']
        ));

        $user->notify(new DatabaseOnlyNotification(
            'uploads/Notification/Transaction.jpg',
            '🎉 Transaction Successful - Plan Upgraded',
            "Great news, {$user->name}! Your payment for the <strong>{$plan->name}</strong> plan has been successfully processed. 🚀<br><br>Your plan is now upgraded, unlocking all the exclusive features and benefits.<br><br>If you have any questions or need assistance, our support team is just a message away.",
            ['web' => "owner/finance/{$transaction->id}/show"]
        ));
    }

}
