<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use App\Models\Plan;
use App\Models\PlanService;
use App\Models\PlanServiceCatalog;
use App\Models\PlanServicePrice;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            $priceCycles = BillingCycle::all()->map(function ($cycle) {
                return [
                    'id' => $cycle->id,
                    'name' => $cycle->duration_months . ' Months',
                ];
            });

            return view('Heights.Admin.Plans.create', [
                'services' => $services,
                'priceCycles' => $priceCycles,
                'currencies' => ['USD', 'PKR'],
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading plan creation form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load plan creation form. Please try again later.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:255|unique:plans,name',
            'plan_description' => 'nullable|string',
            'currency' => 'required|string|size:3',
            'services' => 'required|array',
            'services.*.quantity' => 'required|integer|min:0',
            'services.*.prices' => 'required|array',
            'services.*.prices.*' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $plan = Plan::create([
                'name' => $validated['plan_name'],
                'description' => $validated['plan_description'],
                'currency' => $validated['currency'],
            ]);

            foreach ($validated['services'] as $serviceId => $serviceData) {
                $serviceCatalog = PlanServiceCatalog::findOrFail($serviceId);

                $service = PlanService::create([
                    'plan_id' => $plan->id,
                    'service_catalog_id' => $serviceCatalog->id,
                    'quantity' => $serviceData['quantity'],
                ]);

                foreach ($serviceData['prices'] as $billingCycleId => $price) {
                    PlanServicePrice::create([
                        'service_id' => $service->id,
                        'billing_cycle_id' => $billingCycleId,
                        'price' => $price,
                    ]);
                }
            }


            DB::commit();

            return redirect()->route('plans.index')->with('success', 'Plan created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating plan: ' . $e->getMessage());
            return back()->with('error', 'Failed to create plan. Please try again.');
        }
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

    public function edit($id)
    {
        try {
            $plan = Plan::where('id', $id)
                ->whereHas('services', function ($query) {
                    $query->whereHas('prices');
                })
                ->with([
                    'services' => function ($query) {
                        $query->with(['serviceCatalog', 'prices.billingCycle'])
                            ->whereHas('prices');
                    }
                ])
                ->first();

            if (!$plan) {
                return redirect()->back()->with('error', 'Plan not found or does not match the billing cycle.');
            }

            $services = $plan->services->map(function ($service) {
                $prices = $service->prices->map(function ($price) {
                    $billingCycle = $price->billingCycle;
                    $billingCycleName = $billingCycle ? $billingCycle->duration_months . ' Months' : 'unknown';

                    return [
                        'billing_cycle_id' => $billingCycle->id ?? null,
                        'billing_cycle' => $billingCycleName,
                        'price' => $price->price,
                    ];
                });

                return [
                    'service_id' => $service->id,
                    'service_name' => $service->serviceCatalog->title,
                    'service_description' => $service->serviceCatalog->description,
                    'service_quantity' => $service->quantity,
                    'prices' => $prices,
                ];
            });

            $planDetails = [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'plan_description' => $plan->description,
                'currency' => $plan->currency,
                'services' => $services,
                'updated_at' => $plan->updated_at,
            ];

            $priceCycles = BillingCycle::all()->map(function ($cycle) {
                return [
                    'id' => $cycle->id,
                    'name' => $cycle->duration_months . ' Months',
                ];
            });

            return view('Heights.Admin.Plans.edit', [
                'planDetails' => $planDetails,
                'priceCycles' => $priceCycles,
                'currencies' => ['USD', 'PKR'],
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading plan edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load plan editing form. Please try again later.');
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'plan_name' => 'required|string|max:255|unique:plans,name,' . $request->plan_id . ',id',
            'plan_description' => 'nullable|string',
            'currency' => 'required|string|size:3',
//            'unSelectedCycles' => 'required|array',
//            'unSelectedCycles.*' => 'required|exists:billing_cycles,id',
            'services' => 'required|array',
            'services.*.id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:0',
            'services.*.prices' => 'required|array',
            'services.*.prices.*' => 'required|numeric|min:0',
            'updated_at' => 'required',
        ]);
        $unselectedCycles = $request->input('unSelectedCycles', []);

        try {
            DB::beginTransaction();

            $plan = Plan::where([
                ['id', '=', $validated['plan_id']],
                ['updated_at', '=', $validated['updated_at']]
            ])->sharedLock()->first();

            if (!$plan) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Please refresh the page and try again.');
            }

            $plan->update([
                'name' => $validated['plan_name'],
                'description' => $validated['plan_description'],
                'currency' => $validated['currency'],
            ]);

//            $unselectedCycles = $validated['unSelectedCycles'];

            foreach ($validated['services'] as $serviceData) {
                $service = $plan->services()->findOrFail($serviceData['id']);
                $service->update(['quantity' => $serviceData['quantity']]);

                if (!empty($unselectedCycles)) {
                    PlanServicePrice::where('service_id', $service->id)
                        ->whereIn('billing_cycle_id', $unselectedCycles)
                        ->delete();
                }

                foreach ($serviceData['prices'] as $billingCycleId => $price) {
                    PlanServicePrice::updateOrCreate(
                        [
                            'service_id' => $service->id,
                            'billing_cycle_id' => $billingCycleId
                        ],
                        [
                            'price' => $price
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('plans.index')->with('success', 'Plan updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating plan: ' . $e->getMessage());
            return back()->with('error', 'Failed to update plan. Please try again.');
        }
    }

    public function discontinue($id){

    }
}
