<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSuccessfulCheckout;
use App\Models\Address;
use App\Models\BillingCycle;
use App\Models\Building;
use App\Models\DropdownType;
use App\Models\Organization;
use App\Models\OrganizationPicture;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class OrganizationController extends Controller
{

    public function index()
    {
        try {
            $activeTab = 'Tab1';
            $organizations = Organization::with('address', 'pictures', 'owner')->paginate(10);
            $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City
            $owners = User::where('role_id', 2)
                ->whereNotIn('id', Organization::pluck('owner_id'))
                ->pluck('name', 'id');

            $this->current_plan(1);

            $planCycles = BillingCycle::pluck('duration_months');

            return view('Heights.Admin.Organizations.index', compact('organizations', 'activeTab', 'dropdownData', 'owners', 'planCycles'));
        } catch (\Exception $e) {
            Log::error("Error in index method: " . $e->getMessage());
            return back()->with('error', 'An error occurred while fetching data.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:organizations,name'],
            'email' => 'required|string|email|max:255|unique:organizations,email',
            'phone' => 'required|string|max:255|unique:organizations,phone',
            'owner_id' => ['required', 'integer','unique:organizations,owner_id'],
            'location' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:50'],
            'province' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'is_online_payment_enabled' => ['required', 'in:0,1'],
            'merchant_id' => ['nullable', 'required_if:is_online_payment_enabled,1', 'string', 'max:50'],
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
            'plan_cycle' => 'required|integer',
            'organization_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ] , [
            'merchant_id.required_if' => 'The merchant ID is required when online payments are enabled.',
        ]);

        $billing_cycle_id = $request->plan_cycle_id;

        $plan = Plan::where('id', $request->plan_id)
            ->whereNotIn('status', ['Deleted', 'Inactive'])
            ->whereHas('services', function ($query) use ($billing_cycle_id) {
                $query->with('serviceCatalog')
                    ->whereHas('prices', function ($priceQuery) use ($billing_cycle_id) {
                        $priceQuery->where('billing_cycle_id', $billing_cycle_id);
                    });
            })
            ->with(['services' => function ($query) use ($billing_cycle_id) {
                $query->with('serviceCatalog')
                    ->whereHas('prices', function ($q) use ($billing_cycle_id) {
                        $q->where('billing_cycle_id', $billing_cycle_id);
                    })
                    ->with(['prices' => function ($priceQuery) use ($billing_cycle_id) {
                        $priceQuery->where('billing_cycle_id', $billing_cycle_id);
                    }]);
            }])
            ->first();

        if (!$plan) {
            return redirect()->back()->withInput()->with('error', 'The requested plan is currently unavailable due to administrative changes.');
        }

        $totalPrice = 0;

        $services = $plan->services->map(function ($service) use (&$totalPrice) {
            $price = $service->prices->first();

            if ($price) {
                $totalPrice += $price->price;
            }

            return [
                'service_id' => $service->id,
                'service_catalog_id' => $service->serviceCatalog->id,
                'service_name' => $service->serviceCatalog->title ?? '',
                'service_description' => $service->serviceCatalog->description ?? '',
                'service_quantity' => $service->quantity,
            ];
        });

        $planDetails = [
            'plan_name' => $plan->name,
            'plan_description' => $plan->description,
            'currency' => $plan->currency,
            'total_price' => $totalPrice,
            'services' => $services,
        ];

        DB::beginTransaction();

        try {

            $address = Address::create([
                'location' => $request->location,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            $organization = Organization::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'owner_id' => $request->owner_id,
                'address_id' => $address->id,
                'payment_gateway_merchant_id' => $request->merchant_id,
                'is_online_payment_enabled' => $request->is_online_payment_enabled,
            ]);

            if ($request->hasFile('organization_pictures')) {
                foreach ($request->file('organization_pictures') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = 'uploads/organizations/images/' . $imageName;
                    $image->move(public_path('uploads/organizations/images'), $imageName);

                    OrganizationPicture::create([
                        'organization_id' => $organization->id,
                        'file_path' => $imagePath,
                        'file_name' => $imageName,
                    ]);
                }
            }

            DB::commit();

            ProcessSuccessfulCheckout::dispatch(
                $organization->owner_id,
                $organization->id,
                $plan->id,
                $planDetails,
                $request->plan_cycle,
                'null',
                now(),
                'Cash',
            );

            return redirect()->route('organizations.index')->with('success', 'Organization created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error occurred while creating the organization: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Try again later.');
        }
    }

    public function show(string $id)
    {
        $this->current_plan($id);
    }



    public function edit(string $id)
    {
        $organization = Organization::with('address','pictures')->findOrFail($id);
        $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City
        $owners = User::where('role_id',2)->pluck('name', 'id');



        return view('Heights.Admin.Organizations.edit',compact('organization','dropdownData', 'owners'));
    }

    // add email & phone no
    public function update(Request $request, string $id)
    {
         $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name,' . $id,
            'owner_id' => 'required|integer',
            'status' => 'required|string',
            'location' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:50'],
            'province' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'membership_start_date' => ['required', 'date'],
            'membership_end_date' => ['required', 'date'],
            'organization_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {

            $organization = Organization::findOrFail($id);
            $address = Address::findOrFail($organization->address_id);

            $address->update([
                'location' => $request->location,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            $organization->update([
                'name' => $request->name,
                'owner_id' => $request->owner_id,
                'address_id' => $address->id,
                'status' => $request->status,
                'membership_start_date' => $request->membership_start_date,
                'membership_end_date' => $request->membership_end_date,
            ]);

            if ($request->hasFile('organization_pictures')) {
                foreach ($request->file('organization_pictures') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = 'uploads/organizations/images/' . $imageName;
                    $image->move(public_path('uploads/organizations/images'), $imageName);
                    OrganizationPicture::create([
                        'organization_id' => $organization->id,
                        'file_path' => $imagePath,
                        'file_name' => $imageName,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('organizations.index')->with('success', 'Organization updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Try again later.');
        }
    }

    public function getBuildingsAdmin($id)
    {
        try {
            $buildings = Building::where('organization_id', $id)
                ->whereNotIn('status', ['Under Processing', 'Under Review', 'Rejected'])
                ->pluck('name', 'id');

            return response()->json(['buildings' => $buildings]);

        } catch (\Exception $e) {
            Log::error('Error fetching buildings: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }


    public function destroyImage(string $id)
    {
        $image = OrganizationPicture::findOrFail($id);

        if ($image) {
            $oldImagePath = public_path($image->file_path); // Corrected variable name
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            // Delete the image record from the database
            $image->delete();
        }

        return response()->json(['success' => true]);
    }

    private function current_plan(string $organization_id){

        $subscription = Subscription::where('organization_id', $organization_id)
            ->where('source_name', 'plan')
            ->with([
                'source',
                'planSubscriptionItems:id,subscription_id,service_catalog_id,quantity,used',
                'planSubscriptionItems.serviceCatalog:id,title,icon'
            ])
            ->first();

        $totalUsed = 0;
        $totalQuantity = 0;

        if ($subscription) {
            $formatted = [
                'id' => $subscription->id,
                'name' => $subscription->source->name,
                'billing_cycle' => $subscription->billing_cycle,
                'status' => $subscription->subscription_status,
                'price' => $subscription->price_at_subscription,
                'currency' => $subscription->currency_at_subscription,
                'starts_at' => $subscription->created_at,
                'ends_at' => $subscription->ends_at,
                'services' => $subscription->planSubscriptionItems->map(function ($item) use (&$totalUsed, &$totalQuantity) {
                    $totalUsed += $item->used;
                    $totalQuantity += $item->quantity;

                    return [
                        'service_id' => $item->id,
                        'service_catalog_id' => $item->serviceCatalog->id,
                        'title' => $item->serviceCatalog->title ?? null,
                        'icon' => $item->serviceCatalog->icon ?? null,
                        'quantity' => $item->quantity,
                        'used' => $item->used,
                        'used_percentage' => ($item->quantity > 0) ? number_format(($item->used / $item->quantity) * 100, 2) : 0,
                    ];
                })->toArray(),
            ];
        } else {
            $formatted = null;
        }

        $overallUsedPercentage = ($totalQuantity > 0) ? number_format(($totalUsed / $totalQuantity) * 100, 2) : 0;

        Log::info('Current plan: ' , $formatted);

        return [
            'subscription' => $formatted,
            'overall_used_percentage' => $overallUsedPercentage,
        ];
    }

}
