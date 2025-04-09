<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanServicePrice;
use Illuminate\Support\Facades\Log;


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
                    ->with(['priceForCycle' => fn ($q) => $q->select('*')]);
            }])
            ->get();

        Log::info('Plans : ' . $plans);

        $organizedPlans = $plans->map(function ($plan) {
            return [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'plan_description' => $plan->description,
                'services' => $plan->services->map(function ($service) {
                    return [
                        'service_id' => $service->id,
                        'service_name' => $service->name,
                        'service_quantity' => $service->quantity,
                        'prices' => $service->prices->map(function ($price) {
                            return [
                                'price_id' => $price->id,
                                'amount' => $price->amount,
                                'billing_cycle' => $price->billing_cycle,
                            ];
                        }),
                    ];
                }),
            ];
        });

        Log::info('Plans : ' . $organizedPlans);

        return response()->json(['plans' => $organizedPlans]);
    }

}
