<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use App\Models\Plan;
use App\Models\PlanServicePrice;


class landingController extends Controller
{
    public function index(){
        $planCycles = BillingCycle::pluck('duration_months');

        return view('landing-views.index', compact('planCycles'));
    }

    public function checkout(){
        $planCycles = BillingCycle::pluck('duration_months');

        return view('landing-views.checkout', compact('planCycles'));
    }

    public function plans($planCycle)
    {
        $billing_cycle = BillingCycle::where('duration_months', $planCycle)->first();

        if(!$billing_cycle){
            return response()->json(['plans' => []]);
        }

        $plans = Plan::where('status', 1)
            ->whereHas('services', function ($query) use ($billing_cycle) {
                $query->where('status', 1)
                    ->with('serviceCatalog')
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
            ->get();

        $organizedPlans = $plans->map(function ($plan) use ($billing_cycle) {
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
