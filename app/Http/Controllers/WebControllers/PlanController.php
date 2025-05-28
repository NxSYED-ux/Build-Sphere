<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\BillingCycle;
use App\Models\Plan;
use App\Models\PlanService;
use App\Models\PlanServiceCatalog;
use App\Models\PlanServicePrice;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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

            $plans = Plan::where('status', '!=', 'Deleted')
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
                    'status' => $plan->status,
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

        } catch (\Throwable $e) {
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
                'currencies' => ['PKR'],
                'status' => ['Active', 'Inactive', 'Custom'],
            ]);

        } catch (\Throwable $e) {
            Log::error('Error loading plan creation form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load plan creation form. Please try again later.');
        }
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'name')->where(function ($query) {
                    return $query->where('status', '!=', 'Deleted');
                }),
            ],
            'plan_description' => 'nullable|string',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:Active,Inactive,Custom',
            'services' => 'required|array',
            'services.*.quantity' => 'required|integer|min:0',
            'services.*.prices' => 'required|array',
            'services.*.prices.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $plan = Plan::create([
                'name' => $validated['plan_name'],
                'description' => $validated['plan_description'],
                'currency' => $validated['currency'],
                'status' => $validated['status'],
            ]);

            foreach ($validated['services'] as $serviceId => $serviceData) {
                $serviceCatalog = PlanServiceCatalog::findOrFail($serviceId);
                $parentId = $serviceCatalog?->parent_id;

                $parent_plan_service = null;
                if ($parentId) {
                    $parent_plan_service = PlanService::where('service_catalog_id', $parentId)
                        ->where('plan_id', $plan->id)
                        ->select('quantity')
                        ->first();
                }

                $service = PlanService::create([
                    'plan_id' => $plan->id,
                    'service_catalog_id' => $serviceCatalog->id,
                    'quantity' => $serviceData['quantity'],
                    'meta' => ['quantity' => $parent_plan_service?->quantity],
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

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creating plan: ' . $e->getMessage());
            return back()->with('error', 'Failed to create plan. Please try again.');
        }
    }


    public function show(Request $request, $id)
    {
        try {
            $subscriptionService = new SubscriptionService();

            $selectedPlanCycle = $request->input('planCycle');
            $billing_cycles = BillingCycle::get();
            $billing_cycle = $billing_cycles->where('duration_months', $selectedPlanCycle)->first() ?? $billing_cycles->first();

            if (!$billing_cycle) {
                return redirect()->back()->with('error', 'Billing cycle not found.');
            }

            $plan = $subscriptionService->getValidatedPlanWithBillingCycle($id, $billing_cycle->id, ['Deleted']);

            if (!$plan) {
                return redirect()->back()->with('error', 'Plan not found or does not match the billing cycle.');
            }

            $planDetails = $subscriptionService->getPlanDetailsWithTotalPrice($plan);

            $subscriptions = Subscription::where('source_name', 'plan')
                ->where('source_id', $id)
                ->where('billing_cycle', $billing_cycle->duration_months)
                ->with('organization')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('Heights.Admin.Plans.show', [
                'planDetails' => $planDetails,
                'subscriptions' => $subscriptions,
                'billing_cycles' => $billing_cycles,
                'selected_cycle' => $billing_cycle
            ]);

        } catch (\Throwable $e) {
            Log::error('Error loading plan details: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load plan details. Please try again later.');
        }
    }


    public function edit($id)
    {
        try {
            $plan = Plan::where('id', $id)
                ->where('status', '!=', 'Deleted')
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
                return redirect()->back()->with('error', 'The selected plan was either deleted or does not exist in the system.');
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
                'status' => $plan->status,
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
                'status' => ['Active', 'Inactive', 'Custom'],
            ]);

        } catch (\Throwable $e) {
            Log::error('Error loading plan edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load plan editing form. Please try again later.');
        }
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'plan_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'name')->where(function ($query) {
                    return $query->where('status', '!=', 'Deleted');
                })->ignore($request->plan_id),
            ],
            'plan_description' => 'nullable|string',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:Active,Inactive,Custom',
            'unSelectedCycles' => 'nullable|array',
            'unSelectedCycles.*' => 'nullable|exists:billing_cycles,id',
            'services' => 'required|array',
            'services.*.id' => 'required|exists:planservices,id',
            'services.*.quantity' => 'required|integer|min:0',
            'services.*.prices' => 'required|array',
            'services.*.prices.*' => 'required|numeric|min:0',
            'updated_at' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $plan = Plan::where([
                ['id', '=', $validated['plan_id']],
                ['status', '!=', 'Deleted'],
                ['updated_at', '=', $validated['updated_at']]
            ])->sharedLock()->first();

            if (!$plan) {
                DB::rollBack();
                return redirect()->back()->with('error', 'The plan has been modified or removed by another admin. Please reload the page to get the latest version.');
            }

            if (in_array($plan->status, ['Active', 'Custom']) && $request->status === 'Inactive') {
                $hasActiveSubscriptions = Subscription::where('subscription_status', 'Active')
                    ->where('source_id', $plan->id)
                    ->where('source_name', 'plan')
                    ->exists();

                if ($hasActiveSubscriptions) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Plan '{$plan->name}' cannot be marked as inactive because it has active subscriptions.");
                }
            }

            $plan->update([
                'name' => $validated['plan_name'],
                'description' => $validated['plan_description'],
                'currency' => $validated['currency'],
                'status' => $validated['status'],
            ]);

            $unselectedCycles = $request->input('unSelectedCycles', []);

            foreach ($validated['services'] as $serviceData) {
                $service = $plan->services()->findOrFail($serviceData['id']);
                $serviceCatalog = PlanServiceCatalog::findOrFail($service->service_catalog_id);
                $parentId = $serviceCatalog?->parent_id;

                $parent_plan_service = null;
                if ($parentId) {
                    $parent_plan_service = PlanService::where('service_catalog_id', $parentId)
                        ->where('plan_id', $plan->id)
                        ->select('quantity')
                        ->first();
                }

                $service->update([
                    'quantity' => $serviceData['quantity'],
                    'meta' => ['quantity' => $parent_plan_service?->quantity],
                ]);

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

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error updating plan: ' . $e->getMessage());
            return back()->with('error', 'Failed to update plan. Please try again.');
        }
    }


    public function destroy($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return redirect()->back()->with('error', 'Plan not found');
        }

        $hasActiveSubscriptions = Subscription::where('subscription_status', 'Active')
            ->where('source_id', $plan->id)
            ->where('source_name', 'plan')
            ->exists();

        if ($hasActiveSubscriptions) {
            return redirect()->back()->with('error', "Plan '{$plan->name}' cannot be deleted because it has active subscriptions.");
        }

        $plan->update(['status' => 'Deleted']);

        return redirect()->route('plans.index')->with('success', 'Plan deleted successfully');
    }


    // Helper Functions
    public function activePlans(string $planCycle)
    {
        return $this->plans($planCycle, ['Active']);
    }

    public function activeAndCustomPlans(string $planCycle)
    {
        return $this->plans($planCycle, ['Active', 'Custom']);
    }

    public function organizationPlans(string $planCycle, Request $request)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        $subscription = Subscription::where('organization_id', $organization_id)
            ->where('source_name', 'plan')
            ->latest('created_at')
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
        } catch (\Throwable $e) {
            Log::error('Error fetching plans: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong while fetching plans.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
