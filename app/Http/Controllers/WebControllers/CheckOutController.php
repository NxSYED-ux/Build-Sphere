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
use App\Services\SubscriptionService;
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
        } catch (\Throwable $e) {
            Log::error('Error fetching billing cycles: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function updatePlanOwnerIndex(Request $request)
    {
        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $subscription = Subscription::where('organization_id', $organization_id)
                ->where('source_name', 'plan')
                ->where('subscription_status', 'Active')
                ->first();

            $activePlanId = $subscription?->source_id;
            $activeCycle = $subscription?->billing_cycle;
            $planCycles = BillingCycle::pluck('duration_months');

            return view('Heights.Owner.Plan.upgrade', compact(
                'planCycles',
                'activePlanId',
                'activeCycle'
            ));

        } catch (\Throwable $e) {
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
            $subscriptionService = new SubscriptionService();
            $plan = $subscriptionService->getValidatedPlanWithBillingCycle($request->plan_id, $request->plan_cycle_id);

            if (!$plan) {
                return response()->json(['error' => 'The requested plan is currently unavailable due to administrative changes.'], 404);
            }

            $planDetails = $subscriptionService->getPlanDetailsWithTotalPrice($plan);

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
                return $subscriptionService->handleStripePaymentFailure($e, $plan, $request->plan_cycle, $request, $planDetails);

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
                return $subscriptionService->handleStripePaymentFailure($paymentIntent, $plan, $request->plan_cycle, $request, $planDetails);
            }

            if ($type === 'create') {
                Customer::update($user->customer_payment_id, [
                    'invoice_settings' => [
                        'default_payment_method' => $request->payment_method_id,
                    ],
                ]);
            }

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
                $result = $subscriptionService->handlePlanRenewalOrUpgrade(
                    $user,
                    $organization_id,
                    $plan,
                    $planDetails,
                    $request->plan_cycle,
                    $paymentIntent,
                    'Card'
                );

                if (!empty($result['error'])) {
                    return response()->json(['error' => $result['error']], 400);
                }
            }

            return response()->json(['success' => true]);

        } catch (Exception $e) {
            Log::error('Error while Checkout Plan: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
