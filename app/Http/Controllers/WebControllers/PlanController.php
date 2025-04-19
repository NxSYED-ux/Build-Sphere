<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use App\Models\Plan;
use App\Models\PlanServiceCatalog;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    public function index(Request $request){
        try {
            $selectedPlanCycle = $request->input('planCycle');
            $billing_cycles = BillingCycle::get();
            $billing_cycle = $billing_cycles->where('duration_months', $selectedPlanCycle)->first() ?? $billing_cycles->first();

            if (!$billing_cycle) {
                return view('Heights.Admin.Plans.index', [
                    'plans' => collect(),
                    'billing_cycles' => $billing_cycles,
                    'selected_cycle' => $billing_cycle
                ]);
            }

            $plans = Plan::whereHas('services', function ($query) use ($billing_cycle) {
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
                        'service_name' => optional($service->serviceCatalog)->title,
                        'service_description' => optional($service->serviceCatalog)->description,
                        'service_quantity' => $service->quantity,
                    ];
                });

                return [
                    'id' => $plan->id,
                    'plan_name' => $plan->name,
                    'plan_description' => $plan->description,
                    'currency' => $plan->currency,
                    'total_price' => $totalPrice,
                    'plan_cycle_id' => $billing_cycle->id,
                    'services' => $services,
                ];
            });

            return view('Heights.Admin.Plans.index', [
                'plans' => $organizedPlans,
                'billing_cycles' => $billing_cycles,
                'selected_cycle' => $billing_cycle
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching plans: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading plans, Please try again later.');
        }
    }

    public function create()
    {
        try {
            $services = PlanServiceCatalog::all();
            $priceCycles = BillingCycle::all();

            return view('Heights.Admin.Plans.create', compact('services', 'priceCycles'));
        } catch (\Exception $e) {
            Log::error('Error loading plan creation form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load plan creation form. Please try again later.');
        }
    }


    public function store(Request $request){

    }

    public function show(Request $request, $id)
    {
        try {
            $selectedPlanCycle = $request->input('planCycle');
            $billing_cycles = BillingCycle::get();
            $billing_cycle = $billing_cycles->where('duration_months', $selectedPlanCycle)->first() ?? $billing_cycles->first();

            if (!$billing_cycle) {
                return redirect()->back()->with('error', 'Billing cycle not found.');
            }

            $plan = Plan::where('id', $id)
                ->whereHas('services', function ($query) use ($billing_cycle) {
                    $query->whereHas('prices', function ($priceQuery) use ($billing_cycle) {
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
                ->first();

            if (!$plan) {
                return redirect()->back()->with('error', 'Plan not found or does not match the billing cycle.');
            }

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

            $subscriptions = Subscription::where('source_name', 'plan')
                ->where('source_id', $id)
                ->where('billing_cycle', $cycle)
                ->with('organization', 'organization.pictures')
                ->get();

            Log::info($subscriptions);

            return view('Heights.Admin.Plans.show', [
                'planDetails' => $planDetails,
                'subscriptions' => $subscriptions,
                'billing_cycles' => $billing_cycles,
                'selected_cycle' => $billing_cycle
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading plan details: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load plan details. Please try again later.');
        }
    }

    public function edit(){

    }

    public function update(){

    }

    public function destroy(){

    }

    public function discontinue(){

    }
}
