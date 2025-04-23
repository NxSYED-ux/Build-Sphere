<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class CardController extends Controller
{
    public function getSavedCards(Request $request)
    {
        $user = $request->user() ?? null;
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $customer = Customer::retrieve($user->customer_payment_id);
            $defaultPaymentMethodId = $customer->invoice_settings->default_payment_method ?? null;

            $paymentMethods = PaymentMethod::all([
                'customer' => $user->customer_payment_id,
                'type' => 'card',
            ]);

            $cards = collect($paymentMethods->data)->map(function ($method) use ($defaultPaymentMethodId) {
                return [
                    'id' => $method->id,
                    'brand' => $method->card->brand,
                    'last4' => $method->card->last4,
                    'exp_month' => $method->card->exp_month,
                    'exp_year' => $method->card->exp_year,
                    'is_default' => $method->id === $defaultPaymentMethodId,
                ];
            });

            return response()->json([
                'cards' => $cards,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve saved cards: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve saved cards.'], 500);
        }
    }

    public function addCard(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $user = $request->user() ?? null;
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentMethods = PaymentMethod::all([
                'customer' => $user->customer_payment_id,
                'type' => 'card',
            ]);

            if (count($paymentMethods->data) >= 5) {
                return response()->json(['error' => 'You cannot save more than 5 cards.'], 400);
            }

            $customer = Customer::retrieve($user->customer_payment_id);

            $newPaymentMethod = PaymentMethod::retrieve($request->payment_method_id);

            foreach ($paymentMethods->data as $existingPaymentMethod) {
                if ($existingPaymentMethod->card->last4 === $newPaymentMethod->card->last4 &&
                    $existingPaymentMethod->card->exp_month === $newPaymentMethod->card->exp_month &&
                    $existingPaymentMethod->card->exp_year === $newPaymentMethod->card->exp_year) {
                    return response()->json(['error' => 'This card is already added.'], 400);
                }
            }

            $newPaymentMethod->attach([
                'customer' => $customer->id,
            ]);

            $currentDefault = $customer->invoice_settings->default_payment_method;

            if (!$currentDefault) {
                Customer::update($customer->id, [
                    'invoice_settings' => [
                        'default_payment_method' => $request->payment_method_id,
                    ],
                ]);
            }

            return response()->json([
                'message' => 'Card added successfully.',
                'set_as_default' => !$currentDefault ? true : false,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to add card: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function setDefaultCard(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            Customer::update($user->customer_payment_id, [
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method_id,
                ],
            ]);

            return response()->json([
                'message' => 'Default card updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update default card: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update default card.'], 500);
        }
    }

    public function removeCard(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentMethod = PaymentMethod::retrieve($request->payment_method_id);

            if ($paymentMethod->customer !== $user->customer_payment_id) {
                return response()->json([
                    'error' => 'This card is not attached to the authenticated user.',
                ], 403);
            }

            $activeSubscription = Subscription::where('user_id', $user->id)
                ->where('subscription_status', 'Active')
                ->exists();

            $attachedCards = PaymentMethod::all([
                'customer' => $user->customer_payment_id,
                'type' => 'card',
            ]);

            if (count($attachedCards->data) <= 1 && $activeSubscription) {
                return response()->json(['error' => 'You cannot delete your only card while having an active subscription.'], 403);
            }

            $customer = Customer::retrieve($user->customer_payment_id);
            $isDefault = $customer->invoice_settings->default_payment_method === $paymentMethod->id;

            $paymentMethod->detach();

            if ($isDefault) {
                $remainingCards = PaymentMethod::all([
                    'customer' => $user->customer_payment_id,
                    'type' => 'card',
                ]);

                if (count($remainingCards->data)) {
                    Customer::update($user->customer_payment_id, [
                        'invoice_settings' => [
                            'default_payment_method' => $remainingCards->data[0]->id,
                        ],
                    ]);
                }
            }

            return response()->json([
                'message' => 'Card removed successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to remove card: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to remove card.'], 500);
        }
    }

}
