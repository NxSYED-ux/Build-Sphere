<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use App\Models\Plan;
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

    public function plans($planCycle)
    {
        try {
            $billing_cycle = BillingCycle::where('duration_months', $planCycle)->first();

            if (!$billing_cycle) {
                return response()->json(['plans' => []]);
            }

            $plans = Plan::where('status', 1)
                ->whereHas('services', function ($query) use ($billing_cycle) {
                    $query->with('serviceCatalog')
                        ->whereHas('prices', function ($priceQuery) use ($billing_cycle) {
                            $priceQuery->where('billing_cycle_id', $billing_cycle->id);
                        });
                })
                ->with(['services' => function ($query) use ($billing_cycle) {
                    $query->with('serviceCatalog')
                        ->whereHas('prices', function ($q) use ($billing_cycle) {
                            $q->where('billing_cycle_id', $billing_cycle->id);
                        })
                        ->with(['prices' => function ($priceQuery) use ($billing_cycle) {
                            $priceQuery->where('billing_cycle_id', $billing_cycle->id);
                        }]);
                }])
                ->get();

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


    public function planDetails($id, $planCycle)
    {
        try {
            $billing_cycle = BillingCycle::where('duration_months', $planCycle)->first();

            if (!$billing_cycle) {
                return response()->json(['plan' => null]);
            }

            $plan = Plan::where('id', $id)
                ->where('status', 1)
                ->whereHas('services', function ($query) use ($billing_cycle) {
                    $query->where('status', 1)
                        ->whereHas('prices', function ($priceQuery) use ($billing_cycle) {
                            $priceQuery->where('billing_cycle_id', $billing_cycle->id);
                        });
                })
                ->with(['services' => function ($query) use ($billing_cycle) {
                    $query->where('status', 1)
                        ->with('serviceCatalog')
                        ->whereHas('prices', function ($q) use ($billing_cycle) {
                            $q->where('billing_cycle_id', $billing_cycle->id);
                        })
                        ->with(['prices' => function ($priceQuery) use ($billing_cycle) {
                            $priceQuery->where('billing_cycle_id', $billing_cycle->id);
                        }]);
                }])
                ->first();

            $totalPrice = 0;
            $cycle = $billing_cycle->duration_months;

            $services = $plan->services->map(function ($service) use (&$totalPrice, $cycle) {
                $price = $service->prices->first();

                if ($price) {
                    $totalPrice += $price->price;
                }

                return [
                    'service_id' => $service->id,
                    'service_name' => $service->serviceCatalog->title,
                    'service_description' => $service->serviceCatalog->description,
                    'service_quantity' => $service->quantity,
                    'price' => $price ? [
                        'price' => $price->price,
                        'billing_cycle' => $cycle,
                    ] : null,
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

            return response()->json(['plan' => $planDetails]);
        } catch (\Exception $e) {
            Log::error('Error fetching plan details: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong while fetching the plan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
