<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class landingController extends Controller
{
    public function index()
    {
        try {
            $planCycles = BillingCycle::pluck('duration_months');
        } catch (\Exception $e) {
            Log::error('Error fetching billing cycles: ' . $e->getMessage());
            $planCycles = collect();
        }

        $selectedPlanCycle = $planCycles->first();

        return view('landing-views.index', compact('planCycles', 'selectedPlanCycle'));
    }


    public function activePlans(string $planCycle)
    {
        return $this->plans($planCycle, ['Active']);
    }

    public function activeAndCustomPlans(string $planCycle)
    {
        return $this->plans($planCycle, ['Active', 'Custom']);
    }

    public function orgPlans(string $planCycle, Request $request)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return response()->json(['error' => "Can't access this page, unless you are an organization owner."]);
        }

        $organization_id = $token['organization_id'];

        $subscription = Subscription::where('organization_id', $organization_id)
            ->where('source_name', 'plan')
            ->first();

        $customPlanId = $subscription?->source_id;

        return $this->plans($planCycle, ['Active'], $customPlanId);
    }


    private function plans(string $planCycle, $statuses, int $includeCustomPlanId = null)
    {
        try {
            $billing_cycle = BillingCycle::where('duration_months', $planCycle)->first();

            if (!$billing_cycle) {
                return response()->json(['plans' => []]);
            }

            $plansQuery = Plan::where(function ($query) use ($statuses, $includeCustomPlanId) {
                $query->whereIn('status', $statuses);

                if ($includeCustomPlanId) {
                    $query->orWhere(function ($subQuery) use ($includeCustomPlanId) {
                        $subQuery->where('id', $includeCustomPlanId)
                            ->where('status', 'Custom');
                    });
                }
            });

            $plansQuery->whereHas('services', function ($query) use ($billing_cycle) {
                $query->with('serviceCatalog')
                    ->whereHas('prices', function ($priceQuery) use ($billing_cycle) {
                        $priceQuery->where('billing_cycle_id', $billing_cycle->id);
                    });
            });

            $plans = $plansQuery->with(['services' => function ($query) use ($billing_cycle) {
                $query->with('serviceCatalog')
                    ->whereHas('prices', function ($q) use ($billing_cycle) {
                        $q->where('billing_cycle_id', $billing_cycle->id);
                    })
                    ->with(['prices' => function ($priceQuery) use ($billing_cycle) {
                        $priceQuery->where('billing_cycle_id', $billing_cycle->id);
                    }]);
            }])->get();

            $organizedPlans = $plans->map(function ($plan) use ($billing_cycle) {
                $totalPrice = 0;

                $services = $plan->services->map(function ($service) use (&$totalPrice) {
                    $price = $service->prices->first();

                    if ($price) {
                        $totalPrice += $price->price;
                    }

                    return [
                        'service_id' => $service->id,
                        'service_name' => $service->serviceCatalog->title,
                        'service_description' => $service->serviceCatalog->description,
                        'service_quantity' => $service->quantity,
                    ];
                });

                return [
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'plan_description' => $plan->description,
                    'currency' => $plan->currency,
                    'total_price' => $totalPrice,
                    'services' => $services,
                    'billing_cycle_id' => $billing_cycle->id,
                ];
            });

            return response()->json(['plans' => $organizedPlans]);
        } catch (\Exception $e) {
            Log::error('Error fetching plans: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong while fetching plans.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
