<?php

use App\Models\MembershipUser;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserBuildingUnit;
use App\Notifications\DatabaseOnlyNotification;
use App\Services\AssignUnitService;
use App\Services\MembershipService;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Stripe;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->everySecond();


// Queue Command
Artisan::command('start:queue', function () {
    $this->info('Queue worker started...');
    Artisan::call('queue:work', [
        '--tries' => 3,
        '--stop-when-empty' => true,
    ]);
})->describe('Start the Laravel queue worker');


// Plan Scheduler
Artisan::command('plan', function () {
    $startDate = Carbon::yesterday()->startOfDay();
    $endDate = Carbon::now()->addDays(3)->endOfDay();

    $subscriptions = Subscription::where('source_name', 'plan')
        ->whereBetween('ends_at', [$startDate, $endDate])
        ->where('subscription_status', 'Active')
        ->with('organization')
        ->get();

    foreach ($subscriptions as $subscription) {
        $endsAt = $subscription->ends_at instanceof Carbon
            ? $subscription->ends_at
            : Carbon::parse($subscription->ends_at);

        $endsToday = $endsAt->isSameDay(now());
        $endsIn3Days = $endsAt->isSameDay($endDate);

        if ($endsIn3Days) {
            $subscription->update(['subscription_status' => 'Expired']);
        }

        $organization = $subscription->organization;
        $user = User::find($subscription->user_id);
        if (!$user) continue;

        if (!$user->customer_payment_id) {
            updateOrganizationStatusIfNeeded($organization, $endsToday, $endsIn3Days);
            $user->notify(new DatabaseOnlyNotification(
                null,
                'Automated Plan Payment',
                'Automated payment failed because no card is saved.',
                ''
            ));
            Log::warning("User {$user->id} has no customer_payment_id.");
            continue;
        }

        try {
            DB::beginTransaction();

            $subscriptionService = new SubscriptionService();

            $plan = $subscriptionService->getValidatedPlanWithBillingCycle(
                $subscription->source_id,
                $subscription->billing_cycle
            );

            if (!$plan) {
                Log::warning("Plan not found for subscription ID: {$subscription->id}");
                DB::rollBack();
                continue;
            }

            $planDetails = $subscriptionService->getPlanDetailsWithTotalPrice($plan);

            Stripe::setApiKey(config('services.stripe.secret'));
            $customer = Customer::retrieve($user->customer_payment_id);
            $defaultPaymentMethod = $customer->invoice_settings->default_payment_method ?? null;

            if (!$defaultPaymentMethod) {
                updateOrganizationStatusIfNeeded($organization, $endsToday, $endsIn3Days);

                $user->notify(new DatabaseOnlyNotification(
                    null,
                    'Automated Plan Payment',
                    'Automated payment failed because no default card is found.',
                    ''
                ));

                Log::warning("User {$user->id} has no default card.");
                DB::rollBack();
                continue;
            }

            $paymentIntent = PaymentIntent::create([
                'amount' => $planDetails['total_price'] * 100,
                'currency' => $planDetails['currency'],
                'customer' => $user->customer_payment_id,
                'payment_method' => $defaultPaymentMethod,
                'confirm' => true,
                'off_session' => true,
                'description' => $planDetails['plan_name'] . ': ' . $planDetails['plan_description'],
            ]);

            if ($paymentIntent->status !== 'succeeded') {
                updateOrganizationStatusIfNeeded($organization, $endsToday, $endsIn3Days);

                $user->notify(new DatabaseOnlyNotification(
                    null,
                    'Automated Plan Payment',
                    'Automated payment failed due to a declined or incomplete transaction.',
                    ''
                ));

                Log::error("Payment failed for user {$user->id} on subscription ID: {$subscription->id}");
                DB::rollBack();
                continue;
            }

            Log::info("Payment successful for user {$user->id}, subscription ID: {$subscription->id}");

            $result = $subscriptionService->handlePlanRenewalOrUpgrade(
                $user,
                $subscription->organization_id,
                $plan,
                $planDetails,
                $subscription->billing_cycle,
                $paymentIntent,
                'Card',
                true,
                true
            );

            if (!empty($result['error'])) {
                updateOrganizationStatusIfNeeded($organization, $endsToday, $endsIn3Days);

                try {
                    Refund::create([
                        'payment_intent' => $paymentIntent->id,
                    ]);

                    Log::info("Refund issued for user {$user->id}, payment_intent {$paymentIntent->id} due to failed renewal.");
                } catch (\Exception $refundException) {
                    Log::error("Refund failed for user {$user->id}: " . $refundException->getMessage());

                    $user->notify(new DatabaseOnlyNotification(
                        null,
                        'Automated Plan Refund Failed',
                        'Your payment could not be refunded automatically. Please contact support.',
                        ''
                    ));

                    DB::rollBack();
                    continue;
                }

                $user->notify(new DatabaseOnlyNotification(
                    null,
                    'Plan Renewal Failed',
                    'Your payment was successful but could not be applied to your current plan due to resource limitations. A refund has been issued. Please upgrade to a higher plan to continue using the service.',
                    ''
                ));
            }

            DB::commit();

        } catch (CardException $e) {
            updateOrganizationStatusIfNeeded($organization, $endsToday, $endsIn3Days);

            Log::error("Stripe card error for user {$user->id}: " . $e->getMessage());

            $user->notify(new DatabaseOnlyNotification(
                null,
                'Card Declined',
                'Your card was declined during automatic plan renewal.',
                ''
            ));

            DB::rollBack();
        } catch (Exception $e) {
            updateOrganizationStatusIfNeeded($organization, $endsToday, $endsIn3Days);

            Log::error("General error for user {$user->id}: " . $e->getMessage());

            $user->notify(new DatabaseOnlyNotification(
                null,
                'Payment Processing Error',
                'An unexpected error occurred while processing your payment. Please try again later.',
                ''
            ));

            DB::rollBack();
        }
    }
});


// Rent Scheduler
Artisan::command('rental', function () {
    $yesterday = Carbon::yesterday()->startOfDay();
    $today = Carbon::now()->endOfDay();

    $subscriptions = Subscription::where('source_name', 'unit contract')
        ->whereBetween('ends_at', [$yesterday, $today])
        ->where('subscription_status', 'Active')
        ->get();

    foreach ($subscriptions as $subscription) {
        $endsToday = Carbon::parse($subscription->ends_at)->isSameDay(now());

        if ($endsToday) {
            $subscription->update(['subscription_status' => 'Expired']);
        }

        $unitContract = UserBuildingUnit::with('unit')->find($subscription->source_id);
        $user = User::find($subscription->user_id);

        if (!$user || !$unitContract || !$unitContract->unit) {
            Log::warning("Invalid contract or user for subscription ID: {$subscription->id}");
            continue;
        }

        if (!$user->customer_payment_id) {
            if ($endsToday) {
                $unitContract->update(['contract_status' => 0]);
                $unitContract->unit->update(['availability_status' => 'Available']);
            }

            $user->notify(new DatabaseOnlyNotification(
                null,
                'Automated Rent Payment',
                'Automated payment failed because no card is saved.',
                ''
            ));

            Log::warning("User {$user->id} has no customer_payment_id.");
            continue;
        }

        try {
            DB::beginTransaction();

            Stripe::setApiKey(config('services.stripe.secret'));
            $customer = Customer::retrieve($user->customer_payment_id);
            $defaultPaymentMethod = $customer->invoice_settings->default_payment_method ?? null;

            if (!$defaultPaymentMethod) {
                if ($endsToday) {
                    $unitContract->update(['contract_status' => 0]);
                    $unitContract->unit->update(['availability_status' => 'Available']);
                }

                $user->notify(new DatabaseOnlyNotification(
                    null,
                    'Automated Rent Payment',
                    'Automated payment failed because no default card is found.',
                    ''
                ));

                Log::warning("User {$user->id} has no default card.");
                DB::rollBack();
                continue;
            }

            $paymentIntent = PaymentIntent::create([
                'amount' => $unitContract->price * 100,
                'currency' => 'PKR',
                'customer' => $user->customer_payment_id,
                'payment_method' => $defaultPaymentMethod,
                'confirm' => true,
                'off_session' => true,
                'description' => 'Automated Rent Payment for Contract #' . $unitContract->id,
            ]);

            if ($paymentIntent->status !== 'succeeded') {
                if ($endsToday) {
                    $unitContract->update(['contract_status' => 0]);
                    $unitContract->unit->update(['availability_status' => 'Available']);
                }

                $user->notify(new DatabaseOnlyNotification(
                    null,
                    'Automated Rent Payment',
                    'Automated payment failed due to a declined or incomplete transaction.',
                    ''
                ));

                Log::error("Payment failed for user {$user->id} on subscription ID: {$subscription->id}");
                DB::rollBack();
                continue;
            }

            Log::info("Payment successful for user {$user->id}, subscription ID: {$subscription->id}");

            $assignUnitService = new AssignUnitService();
            [$assignedUnit, $transaction] = $assignUnitService->unitAssignment_Transaction(
                $user,
                $unitContract->unit,
                $unitContract->type,
                $paymentIntent->id,
                $unitContract->price,
                $unitContract->billing_cycle,
                'Card',
                'PKR',
                'Renew',
                $unitContract,
                $subscription
            );

            $assignUnitService->sendUnitAssignmentNotifications(
                $unitContract->unit,
                $transaction,
                $user->id,
                $assignedUnit
            );

            DB::commit();

        } catch (CardException $e) {
            if ($endsToday) {
                $unitContract->update(['contract_status' => 0]);
                $unitContract->unit->update(['availability_status' => 'Available']);
            }

            DB::rollBack();

            Log::error("Stripe card error for user {$user->id}: " . $e->getMessage());

            $user->notify(new DatabaseOnlyNotification(
                null,
                'Card Declined',
                'Your card was declined during automatic rent renewal.',
                ''
            ));

        } catch (Exception $e) {
            if ($endsToday) {
                $unitContract->update(['contract_status' => 0]);
                $unitContract->unit->update(['availability_status' => 'Available']);
            }

            DB::rollBack();

            Log::error("General error for user {$user->id}: " . $e->getMessage());

            $user->notify(new DatabaseOnlyNotification(
                null,
                'Payment Processing Error',
                'An unexpected error occurred while processing your payment. Please try again later.',
                ''
            ));
        }
    }
});


// Membership Scheduler
Artisan::command('membership_subscriptions', function () {
    $yesterday = Carbon::yesterday()->startOfDay();
    $today = Carbon::now()->endOfDay();

    $subscriptions = Subscription::where('source_name', 'membership')
        ->whereBetween('ends_at', [$yesterday, $today])
        ->where('subscription_status', 'Active')
        ->get();

    foreach ($subscriptions as $subscription) {
        $endsToday = Carbon::parse($subscription->ends_at)->isSameDay(now());

        if ($endsToday) {
            $subscription->update(['subscription_status' => 'Expired']);
        }

        $membershipUser = UserBuildingUnit::with('membership')->find($subscription->source_id);
        $user = User::find($subscription->user_id);
        $membership = $membershipUser?->membership;

        if (!$user || !$membershipUser || !$membership) {
            Log::warning("Invalid user, membershipUser or membership for subscription ID: {$subscription->id}");
            continue;
        }

        if (!$user->customer_payment_id) {
            if ($endsToday) {
                $membershipUser->update(['status' => 0]);
            }

            $user->notify(new DatabaseOnlyNotification(
                null,
                'Automated Membership Payment',
                'Automated payment failed because no card is saved.',
                ''
            ));

            Log::warning("User {$user->id} has no customer_payment_id.");
            continue;
        }

        try {
            DB::beginTransaction();

            Stripe::setApiKey(config('services.stripe.secret'));

            $customer = Customer::retrieve($user->customer_payment_id);
            $defaultPaymentMethod = $customer->invoice_settings->default_payment_method ?? null;

            if (!$defaultPaymentMethod) {
                if ($endsToday) {
                    $membershipUser->update(['status' => 0]);
                }

                $user->notify(new DatabaseOnlyNotification(
                    null,
                    'Automated Membership Payment',
                    'Automated payment failed because no default card is found.',
                    ''
                ));

                Log::warning("User {$user->id} has no default card.");
                DB::rollBack();
                continue;
            }

            $paymentIntent = PaymentIntent::create([
                'amount' => $membershipUser->price * 100,
                'currency' => 'PKR',
                'customer' => $user->customer_payment_id,
                'payment_method' => $defaultPaymentMethod,
                'confirm' => true,
                'off_session' => true,
                'description' => 'Automated Membership Payment for Membership User #' . $membershipUser->id,
            ]);

            if ($paymentIntent->status !== 'succeeded') {
                if ($endsToday) {
                    $membershipUser->update(['status' => 0]);
                }

                $user->notify(new DatabaseOnlyNotification(
                    null,
                    'Automated Membership Payment',
                    'Automated payment failed due to a declined or incomplete transaction.',
                    ''
                ));

                Log::error("Payment failed for user {$user->id} on subscription ID: {$subscription->id}");
                DB::rollBack();
                continue;
            }

            Log::info("Payment successful for user {$user->id}, subscription ID: {$subscription->id}");

            $membershipService = new MembershipService();

            $transaction = $membershipService->membershipAssignment_Transaction(
                $user,
                $membership,
                $paymentIntent->id,
                'Card',
                'Renew',
                $membershipUser,
                $subscription
            );

            $membershipService->sendMembershipSuccessNotifications(
                $membership,
                $transaction,
                $user
            );

            DB::commit();

        } catch (CardException $e) {
            if ($endsToday) {
                $membershipUser->update(['status' => 0]);
            }

            DB::rollBack();

            Log::error("Stripe card error for user {$user->id}: " . $e->getMessage());

            $user->notify(new DatabaseOnlyNotification(
                null,
                'Card Declined',
                'Your card was declined during automatic membership renewal.',
                ''
            ));

        } catch (Exception $e) {
            if ($endsToday) {
                $membershipUser->update(['status' => 0]);
            }

            DB::rollBack();

            Log::error("General error for user {$user->id}: " . $e->getMessage());

            $user->notify(new DatabaseOnlyNotification(
                null,
                'Payment Processing Error',
                'An unexpected error occurred while processing your membership payment. Please try again later.',
                ''
            ));
        }
    }
});


// Membership Usage
Artisan::command('update_membership_usage', function () {
    $today = Carbon::now()->endOfDay();

    $MembershipUsers = MembershipUser::where('source_name', 'membership')
        ->where('ends_at', '<=', $today)
        ->get();

    foreach ($MembershipUsers as $membershipUser) {
        $membershipUser->update([
            'used' => $membershipUser->quantity,
        ]);
    }

    Log::info("Membership usage updated for " . $MembershipUsers->count() . " records.");
});


// Cancelled Subscriptions Scheduler
Artisan::command('canceled_Subscriptions', function () {
    $subscriptions = Subscription::whereDate('ends_at', now()->toDateString())
        ->where('subscription_status', 'Canceled')
        ->get();

    foreach ($subscriptions as $subscription) {
        $subscription->update([
            'subscription_status' => 'Expired'
        ]);

        if ($subscription->source_name === 'plan') {
            $organization = Organization::find($subscription->organization_id);
            if ($organization) {
                $organization->update([
                    'status' => 'Blocked',
                ]);
            }

        } elseif ($subscription->source_name === 'membership') {
            $membershipUser = MembershipUser::find($subscription->source_id);
            if ($membershipUser) {
                $membershipUser->update([
                    'status' => 0,
                ]);
            }

        } elseif ($subscription->source_name === 'unit contract') {
            $unitContract = UserBuildingUnit::with('unit')->find($subscription->source_id);
            if ($unitContract) {
                $unitContract->update([
                    'contract_status' => 0,
                ]);

                if ($unitContract->unit) {
                    $unitContract->unit->update([
                        'availability_status' => 'Available',
                    ]);
                }
            }
        }
    }
});


//Helper Function
function updateOrganizationStatusIfNeeded($organization, bool $endsToday, bool $endsIn3Days): void
{
    if ($endsIn3Days) {
        $organization->update(['status' => 'Blocked']);
    } elseif ($endsToday) {
        $organization->update(['status' => 'Disable']);
    }
}

