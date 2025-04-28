<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSuccessfulCheckout;
use App\Models\BillingCycle;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CheckOutController extends Controller
{
    public function index(Request $request)
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

    public function checkout(Request $request)
    {
        $request->validate([
            'owner_id' => 'required',
            'organization_id' => 'required',
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
            'plan_cycle' => 'required|integer',
            'payment_method_id' => 'required|string',
        ]);

        try {

            $user = User::find($request->owner_id);
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            $organization = Organization::where('id', $request->organization_id)
                ->where('status', 'Enable')
                ->first();

            if ($organization) {
                return response()->json(['error' => 'An active subscription already exists for your organization.'], 409);
            }

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

}
