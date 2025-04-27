<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use App\Models\Plan;
use Illuminate\Support\Facades\Log;


class UpgradePlanController extends Controller
{
    public function index($planCycle)
    {
        try {
            $billing_cycle = BillingCycle::where('duration_months', $planCycle)->first();

            if (!$billing_cycle) {
                return response()->json(['plans' => []]);
            }

            $plans = Plan::where('status', 'Active')
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

}
