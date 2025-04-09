<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanServicePrice;


class landingController extends Controller
{
    public function index(){
        $planCycles = PlanServicePrice::select('billing_cycle')
            ->distinct()
            ->pluck('billing_cycle');

        return view('landing-views.index', compact('planCycles'));
    }

    public function checkout(){
        return view('landing-views.checkout');
    }

    public function plans($planCycle)
    {
        $plans = Plan::where('status', 1)
            ->whereHas('services', function ($query) use ($planCycle) {
                $query->where('status', 1)
                    ->whereHas('prices', function ($priceQuery) use ($planCycle) {
                        $priceQuery->where('billing_cycle', $planCycle);
                    });
            })
            ->with(['services' => function ($query) use ($planCycle) {
                $query->where('status', 1)
                    ->whereHas('prices', function ($q) use ($planCycle) {
                        $q->where('billing_cycle', $planCycle);
                    })
                    ->with(['prices' => function ($priceQuery) use ($planCycle) {
                        $priceQuery->where('billing_cycle', $planCycle);
                    }]);
            }])
            ->get();

        $organizedPlans = $plans->map(function ($plan) {
            $totalPrice = 0;

            $services = $plan->services->map(function ($service) use (&$totalPrice) {
                $price = $service->prices->first();

                if ($price) {
                    $totalPrice += $price->price;
                }

                return [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'service_quantity' => $service->quantity,
                    'price' => $price ? [
                        'price' => $price->price,
                        'billing_cycle' => $price->billing_cycle,
                    ] : null,
                ];
            });

            return [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'plan_description' => $plan->description,
                'currency' => $plan->currency,
                'total_price' => $totalPrice,
                'services' => $services,
            ];
        });

        return response()->json(['plans' => $organizedPlans]);
    }

}
