<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;


class CheckOutController extends Controller
{
    public function index(Request $request){
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'organization_name' => 'required|string|exists:organizations,name',
        ]);
        try {
            $email = $request->input('email');
            $organization_name = $request->input('organization_name');
            $selectedPackage = $request->input('package');
            $selectedCycle = $request->input('cycle');
            $planCycles = BillingCycle::pluck('duration_months');
        } catch (\Exception $e) {
            Log::error('Error fetching billing cycles: ' . $e->getMessage());
            $planCycles = collect();
        }

        return view('landing-views.checkout', compact('planCycles', 'email', 'selectedPackage', 'selectedCycle'));
    }


    public function checkout(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
            'plan_cycle' => 'required',
            'payment_method_id' => 'required|string',
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            $billing_cycle_id = $request->plan_cycle_id;

            $plan = Plan::where('id', $request->plan_id)
                ->where('status', 1)
                ->whereHas('services', function ($query) use ($billing_cycle_id) {
                    $query->where('status', 1)
                        ->whereHas('prices', function ($priceQuery) use ($billing_cycle_id) {
                            $priceQuery->where('billing_cycle_id', $billing_cycle_id);
                        });
                })
                ->with(['services' => function ($query) use ($billing_cycle_id) {
                    $query->where('status', 1)
                        ->with('serviceCatalog')
                        ->whereHas('prices', function ($q) use ($billing_cycle_id) {
                            $q->where('billing_cycle_id', $billing_cycle_id);
                        })
                        ->with(['prices' => function ($priceQuery) use ($billing_cycle_id) {
                            $priceQuery->where('billing_cycle_id', $billing_cycle_id);
                        }]);
                }])
                ->first();

            if (!$plan) {
                return response()->json(['error' => 'Valid plan not found for the selected billing cycle.'], 404);
            }

            $totalPrice = 0;

            $services = $plan->services->map(function ($service) use (&$totalPrice) {
                $price = $service->prices->first();

                if ($price) {
                    $totalPrice += $price->price;
                }

                return [
                    'service_id' => $service->id,
                    'service_name' => $service->serviceCatalog->title ?? '',
                    'service_description' => $service->serviceCatalog->description ?? '',
                    'service_quantity' => $service->quantity,
                ];
            });

            $planDetails = [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'plan_description' => $plan->description,
                'currency' => $plan->currency,
                'total_price' => $totalPrice,
                'services' => $services,
            ];

            Stripe::setApiKey(config('services.stripe.secret'));

            $customer = Customer::retrieve($user->customer_payment_id);

            PaymentMethod::retrieve($request->payment_method_id)->attach([
                'customer' => $customer->id,
            ]);

            Customer::update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method_id,
                ],
            ]);


            $paymentIntent = PaymentIntent::create([
                'amount' => $planDetails['total_price'] * 100,
                'currency' => $planDetails['currency'],
                'customer' => $user->customer_payment_id,
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'description' => $planDetails['plan_name'] . ': ' . $planDetails['plan_description'],
                'return_url' => route('checkout.success'),
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

            if ($paymentIntent->status === 'succeeded') {

                return response()->json(['success' => true]);
            }

            Log::warning('Stripe payment not succeeded', ['status' => $paymentIntent->status]);

            return response()->json([
                'error' => 'Payment failed.',
                'status' => $paymentIntent->status,
            ]);

        } catch (\Exception $e) {
            Log::error('Error while Checkout Plan: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
