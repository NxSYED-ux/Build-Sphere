<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\OrganizationOwnerNotifications;
use App\Jobs\ProcessSuccessfulCheckout;
use App\Jobs\SendRoleNotification;
use App\Models\Address;
use App\Models\BillingCycle;
use App\Models\Building;
use App\Models\DropdownType;
use App\Models\Organization;
use App\Models\OrganizationPicture;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\DatabaseOnlyNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class OrganizationController extends Controller
{
    // Index Function
    public function index()
    {
        try {
            $activeTab = 'Tab1';
            $organizations = Organization::with('address', 'owner')->paginate(10);
            $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City
            $owners = User::where('role_id', 2)
                ->whereNotIn('id', Organization::pluck('owner_id'))
                ->pluck('name', 'id');

            $planCycles = BillingCycle::pluck('duration_months');

            return view('Heights.Admin.Organizations.index', compact('organizations', 'activeTab', 'dropdownData', 'owners', 'planCycles'));
        } catch (\Exception $e) {
            Log::error("Error in index method: " . $e->getMessage());
            return back()->with('error', 'An error occurred while fetching data.');
        }
    }


    // Store Function
    public function store(Request $request)
    {
        $user = $request->user() ?? abort(401, 'Unauthorized action.');

        $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name',
            'email' => 'required|string|email|max:255|unique:organizations,email',
            'phone' => 'required|string|max:255|unique:organizations,phone',
            'owner_id' => 'required|integer|unique:organizations,owner_id',
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:50',
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'postal_code' => 'required|string|max:50',
            'is_online_payment_enabled' => 'required|in:0,1',
            'merchant_id' => 'nullable|required_if:is_online_payment_enabled,1|string|max:50',
            'plan_id' => 'required|exists:plans,id',
            'plan_cycle_id' => 'required|exists:billing_cycles,id',
            'plan_cycle' => 'required|integer',
            'organization_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
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

            $logo = null;
            if ($request->hasFile('organization_pictures')) {
                $firstImage = $request->file('organization_pictures')[0] ?? null;

                if ($firstImage) {
                    $imageName = time() . '_' . $firstImage->getClientOriginalName();
                    $imagePath = 'uploads/organizations/logo/' . $imageName;
                    $firstImage->move(public_path('uploads/organizations/logo'), $imageName);
                    $logo = $imagePath;
                }
            }

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
                'logo' => $logo,
                'owner_id' => $request->owner_id,
                'address_id' => $address->id,
                'payment_gateway_merchant_id' => $request->merchant_id,
                'is_online_payment_enabled' => $request->is_online_payment_enabled,
            ]);

            if ($request->hasFile('organization_pictures')) {
                foreach ($request->file('organization_pictures') as $index => $image) {
                    if ($index === 0) {
                        continue;
                    }
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
                'Cash'
            );

            dispatch( new SendRoleNotification(
                1,
                $logo ?? 'uploads/Notification/Light-theme-Logo.svg',
                "{$organization->name} Added Successfully by {$user->name}",
                "{$organization->name} has been successfully added to our platform by {$user->name}. The organization is now active with the {$plan->name} plan, which includes all the amazing features and benefits.",
                ['web' => "admin/organizations/{$organization->id}/show"],

                $user->id,
                "{$organization->name} Added Successfully",
                "{$organization->name} has been successfully added to the platform with the {$plan->name} plan by {$user->email}. The organization is now live and fully operational.",
                ['web' => "admin/organizations/{$organization->id}/show"],
            ));

            return redirect()->route('organizations.index')->with('success', 'Organization created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error occurred while creating the organization: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Try again later.');
        }
    }


    // Show Function
    public function show(string $id, String $portal = 'admin')
    {
        $view = $portal === 'admin' ? 'Heights.Admin.Organizations.show' : 'Heights.Owner.Organization.profile';
        try {
            $organization = Organization::with('address','pictures', 'owner')->findOrFail($id);
            $planDetails = $this->current_plan($id);
            $subscription = $planDetails['subscription'];
            $usage = $planDetails['usage'];

            return view($view, compact('organization', 'subscription', 'usage'));

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Organization not found.');
        }
    }


    // Edit Functions
    public function edit(string $id, string $portal = 'admin')
    {
        $organization = Organization::with('address','pictures')->findOrFail($id);
        $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City

        if($portal === 'admin'){
            $owners = User::where('role_id',2)->pluck('name', 'id');
            return view('Heights.Admin.Organizations.edit',compact('organization','dropdownData', 'owners'));
        } elseif ($portal === 'owner'){
            return view('Heights.Owner.Organization.edit',compact('organization','dropdownData'));
        }else{
            abort(404, 'Page not found.');
        }
    }

    public function ownerEdit(Request $request)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->with('error', "Can't access this page, unless you are an organization owner.");
        }

        return $this->edit($token['organization_id'], 'owner');
    }


    // Update Functions
    public function adminUpdate(Request $request, String $id)
    {
        return $this->update($request, $id, 'admin');
    }

    public function ownerUpdate(Request $request)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->with('error', "Can't access this page, unless you are an organization owner.");
        }

        return $this->update($request, $token['organization_id'], 'owner');
    }

    private function update(Request $request, string $id, string $portal)
    {
        $user = $request->user() ?? abort(404, 'Unauthorized action.');

        $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name,' . $id . ',id',
            'email' => 'required|string|email|max:255|unique:organizations,email,' . $id . ',id',
            'phone' => 'required|string|max:255|unique:organizations,phone,' . $id . ',id',
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:50',
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'postal_code' => 'required|string|max:50',
            'is_online_payment_enabled' => 'required|in:0,1',
            'merchant_id' => 'nullable|required_if:is_online_payment_enabled,1|string|max:50',
            'organization_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'merchant_id.required_if' => 'The merchant ID is required when online payments are enabled.',
        ]);

        $organization = Organization::find($id);
        if (!$organization) return redirect()->back()->withInput()->with('error', 'Organization not found.');

        $address = Address::find($organization->address_id);
        if (!$address) return redirect()->back()->withInput()->with('error', 'Address for the organization not found.');

        DB::beginTransaction();

        try {
            $address->update([
                'location' => $request->location,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            $organization->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
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

            if ($portal === 'admin') {
                $route = 'organizations.index';
                dispatch(new OrganizationOwnerNotifications(
                    $id,
                    null,
                    "Organization Updated by Admin",
                    "Your organization's details have been successfully updated by the admin. You can review the changes by clicking this notification.",
                    "owner/organization",

                    false,
                    $user->id,
                    'Organization Updated Successfully',
                    "The details of {$organization->name} has been successfully updated. Click the notification to review the updated details.",
                    "admin/organizations/{$organization->id}/show",
                ));

            }else{
                $route = 'owner.organization.profile';
                $user->notify(new DatabaseOnlyNotification(
                    null,
                    'Organization Details Updated',
                    'The details of your organization have been successfully updated. You can review the updated information by clicking the notification.',
                    ['web' => "owner/organization"],
                ));
            }

            return redirect()->route($route)->with('success', 'Organization updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error occurred while updating the organization: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Try again later.');
        }
    }


    // Organization Profile Function
    public function organizationProfile(Request $request){

        $token = $request->attributes->get('token');

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->with('error', "Can't access this page, unless you are an organization owner.");
        }
        return $this->show($token['organization_id'], 'owner');
    }


    // Update Logo
    public function adminUpdateLogo(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        return $this->updateLogo($request, $request->id);
    }

    public function ownerUpdateLogo(Request $request)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return response()->json(['error' => "Can't access this page, unless you are an organization owner."]);
        }

        return $this->updateLogo($request, $token['organization_id']);
    }

    private function updateLogo(Request $request, string $id)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $organization = Organization::findOrFail($id);

            $newImage = $request->file('logo');
            $imageName = time() . '_' . $newImage->getClientOriginalName();
            $imagePath = 'uploads/organizations/logo/' . $imageName;
            $newImage->move(public_path('uploads/organizations/logo'), $imageName);

            if ($organization->logo && File::exists(public_path($organization->logo))) {
                File::delete(public_path($organization->logo));
            }

            $organization->update([
                'logo' => $imagePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logo updated successfully.',
                'logo' => $imagePath,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update logo for organization ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to update logo. Please try again later.'], 500);
        }
    }


    // Online Payment Status
    public function adminOnlinePaymentStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        return $this->updateOnlinePaymentStatus($request, $request->id, 'admin');
    }

    public function ownerOnlinePaymentStatus(Request $request)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return response()->json(['error' => "Can't access this page, unless you are an organization owner."]);
        }

        return $this->updateOnlinePaymentStatus($request, $token['organization_id'], 'owner');
    }

    private function updateOnlinePaymentStatus(Request $request, string $id, string $portal)
    {
        $user = $request->user() ?? abort(404, 'Unauthorized action.');

        $request->validate([
            'is_online_payment_enabled' => 'required|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            $organization = Organization::where('id', $id)->sharedLock()->first();

            if (!$organization) {
                DB::rollBack();
                return response()->json(['error' => 'The organization you are trying to update was not found.'], 404);
            }

            if (empty($organization->payment_gateway_merchant_id) && (int) $request->is_online_payment_enabled === 1) {
                DB::rollBack();
                return response()->json(['error' => 'Online payment cannot be enabled because the organization is not linked to any payment gateway.'], 422);
            }

            $organization->update([
                'is_online_payment_enabled' => $request->is_online_payment_enabled,
            ]);

            $statusText = $request->is_online_payment_enabled ? 'enabled' : 'disabled';

            if ($portal === 'admin') {
                dispatch(new OrganizationOwnerNotifications(
                    $organization->id,
                    null,
                    "Online Payment Option {$statusText} by Admin",
                    "The admin has {$statusText} the online payment option for your organization. Click here to review the updated settings.",
                    "owner/organization",
                    false,
                    $user->id,
                    "Online Payment Option {$statusText}",
                    "You have successfully {$statusText} the online payment option for {$organization->name}. Click here to view the organization details.",
                    "admin/organizations/{$organization->id}/show"
                ));
            } else {
                $user->notify(new DatabaseOnlyNotification(
                    null,
                    "Online Payment Option {$statusText}",
                    "You have successfully {$statusText} the online payment option for your organization. Click here to review the changes.",
                    ['web' => "owner/organization"],
                ));
            }

            DB::commit();

            return response()->json(['message' => "Organization's online payment option has been {$statusText} successfully."], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Organization online payment status update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An unexpected error occurred while updating the organization. Please try again later.');
        }
    }


    // Payment Received
    public function planPaymentReceived(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');

        $request->validate([
            'id' => 'required|exists:organizations,id',
        ]);

        try {
            DB::beginTransaction();

            $organization = Organization::findOrFail($request->id);

            $planSubscription = Subscription::where('organization_id', $organization->id)
                ->where('source_name', 'plan')
                ->where('subscription_status', 'Active')
                ->firstOrFail();

            $billingCycle = BillingCycle::where('duration_months', $planSubscription->billing_cycle)
                ->firstOrFail();

            $planDetails = $this->getValidatedPlanWithTotalPrice($planSubscription->source_id, $billingCycle->id);

            if (!$planDetails) {
                throw new \Exception('Invalid plan configuration');
            }

            if ($organization->status !== 'Enable') {
                $organization->update(['status' => 'Enable']);
            }

            $transaction = Transaction::create([
                'transaction_title' => "{$planDetails['name']} ({$billingCycle->duration_months} Months)",
                'transaction_category' => 'Renewal',
                'buyer_id' => $organization->id,
                'buyer_type' => 'organization',
                'seller_type' => 'platform',
                'payment_method' => 'Cash',
                'price' => $planDetails['total_price'],
                'currency' => $planDetails['currency'],
                'status' => 'Completed',
                'is_subscription' => true,
                'billing_cycle' => $billingCycle->duration_months . ' Months',
                'subscription_start_date' => now(),
                'subscription_end_date' => now()->addMonths($billingCycle->duration_months),
                'source_id' => $planSubscription->id,
                'source_name' => 'subscription',
            ]);

            $planSubscription->update([
                'ends_at' => now()->addMonths($billingCycle->duration_months),
                'price_at_subscription' => $planDetails['total_price'],
                'currency_at_subscription' => $planDetails['currency'],
            ]);

            DB::commit();

            dispatch(new OrganizationOwnerNotifications(
                $organization->id,
                null,
                'Payment Marked as Received by Admin',
                "An admin has marked your subscription payment as received. Your plan has been renewed for {$billingCycle->duration_months} months. You can view the transaction details for more information.",
                "owner/finance/{$transaction->id}/show",
                false,

                $user->id,
                'Payment Successfully Marked as Received',
                "Payment marked as received for {$organization->name}. Subscription renewed for {$billingCycle->duration_months} months.",
                "admin/organizations/{$organization->id}/show",
            ));

            return redirect()->back()->with('success', 'Marked as Payment Received successfully.');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            $errorMessage = match (true) {
                $e->getModel() === Organization::class => 'Organization not found',
                $e->getModel() === Subscription::class => 'No active subscription found',
                $e->getModel() === BillingCycle::class => 'Invalid billing cycle configuration',
                default => 'Invalid reference data'
            };
            return redirect()->back()->with('error', $errorMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Plan payment processing failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }


    // Cancel Plan
    public function adminCancelPlanSubscription(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:organizations,id',
        ]);

        return $this->cancelPlanSubscription($request, $request->id, 'admin');
    }

    public function ownerCancelPlanSubscription(Request $request)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return redirect()->back()->with('error', "Can't access this page, unless you are an organization owner.");
        }

        return $this->cancelPlanSubscription($request, $token['organization_id'], 'owner');
    }

    private function cancelPlanSubscription(Request $request, string $id, string $portal)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');

        try {
            DB::beginTransaction();

            $organization = Organization::findOrFail($id);

            $planSubscription = Subscription::where('organization_id', $organization->id)
                ->where('source_name', 'plan')
                ->where('subscription_status', 'Active')
                ->firstOrFail();

            $planSubscription->update([
                'subscription_status' => 'Cancelled',
            ]);

            DB::commit();

            if ($portal === 'admin') {
                dispatch(new OrganizationOwnerNotifications(
                    $organization->id,
                    null,
                    "Plan Cancellation Scheduled by Admin",
                    "An admin has scheduled the cancellation of your current plan. It will remain active until the end of the paid period. You can reactivate it anytime before the period ends if you wish to continue.",
                    "owner/organization",

                    false,

                    $user->id,
                    "Plan Cancellation Scheduled",
                    "The plan subscription for {$organization->name} has been scheduled for cancellation after the current billing period.",
                    "admin/organizations/{$organization->id}/show"
                ));
            } else {
                dispatch(new DatabaseOnlyNotification(
                    null,
                    "Plan Cancellation Scheduled",
                    "Your current plan will remain active until the end of the paid period. Renewal has been disabled, but you can reactivate it anytime before the period ends.",
                    "owner/organization"
                ));
            }

            return redirect()->back()->with('success', 'Plan subscription cancellation has been scheduled successfully.');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            $errorMessage = match (true) {
                $e->getModel() === Organization::class => 'Organization not found',
                $e->getModel() === Subscription::class => 'No active plan subscription found',
                default => 'Invalid reference data'
            };
            return redirect()->back()->with('error', $errorMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Plan subscription cancellation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Plan cancellation failed: ' . $e->getMessage());
        }
    }


    // Resume Plan
    public function adminResumePlanSubscription(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:organizations,id',
        ]);

        return $this->resumePlanSubscription($request, $request->id, 'admin');
    }

    public function ownerResumePlanSubscription(Request $request)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return redirect()->back()->with('error', "Can't access this page, unless you are an organization owner.");
        }

        return $this->resumePlanSubscription($request, $token['organization_id'], 'owner');
    }

    private function resumePlanSubscription(Request $request, string $id, string $portal)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');

        try {
            DB::beginTransaction();

            $organization = Organization::findOrFail($id);

            $planSubscription = Subscription::where('organization_id', $organization->id)
                ->where('source_name', 'plan')
                ->where('subscription_status', 'Cancelled')
                ->firstOrFail();

            $planSubscription->update([
                'subscription_status' => 'Active',
            ]);

            DB::commit();

            if ($portal === 'admin') {
                dispatch(new OrganizationOwnerNotifications(
                    $organization->id,
                    null,
                    "Plan Resumed by Admin",
                    "An admin has resumed your plan. Your subscription will now continue normally without interruption.",
                    "owner/organization",

                    false,

                    $user->id,
                    "Plan Resumed Successfully",
                    "The plan subscription for {$organization->name} has been resumed and is now active.",
                    "admin/organizations/{$organization->id}/show"
                ));
            } else {
                dispatch(new DatabaseOnlyNotification(
                    null,
                    "Plan Resumed Successfully",
                    "Your plan subscription has been resumed. It will continue without interruption.",
                    "owner/organization"
                ));
            }

            return redirect()->back()->with('success', 'Plan subscription has been resumed successfully.');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            $errorMessage = match (true) {
                $e->getModel() === Organization::class => 'Organization not found',
                $e->getModel() === Subscription::class => 'No cancelled plan subscription found',
                default => 'Invalid reference data'
            };
            return redirect()->back()->with('error', $errorMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Plan subscription resume failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Plan resuming failed: ' . $e->getMessage());
        }
    }


    // Helper Functions
    private function current_plan(string $organization_id)
    {
        try {
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

            return [
                'subscription' => $formatted,
                'usage' => $overallUsedPercentage,
            ];

        } catch (\Exception $e) {
            Log::error('Error occurred while fetching current plan for organization ID ' . $organization_id . ': ' . $e->getMessage());
            return [
                'subscription' => null,
                'usage' => 0,
            ];
        }
    }

    private function getValidatedPlanWithTotalPrice($planId, $billingCycleId)
    {
        $plan = Plan::where('id', $planId)
            ->where('status', '!=', 'Deleted')
            ->select('id', 'name', 'description')
            ->whereHas('services.prices', function($query) use ($billingCycleId) {
                $query->where('billing_cycle_id', $billingCycleId);
            })
            ->with(['services' => function($query) use ($billingCycleId) {
                $query->whereHas('prices', function($q) use ($billingCycleId) {
                    $q->where('billing_cycle_id', $billingCycleId);
                })
                    ->with(['prices' => function($priceQuery) use ($billingCycleId) {
                        $priceQuery->where('billing_cycle_id', $billingCycleId)
                            ->select('price', 'service_id');
                    }]);
            }])
            ->first();

        if (!$plan) {
            return null;
        }

        $totalPrice = $plan->services->sum(function($service) {
            return $service->prices->first()->price;
        });

        return [
            'name' => $plan->name,
            'description' => $plan->description,
            'total_price' => $totalPrice,
            'currency' => $plan->currency,
        ];
    }


    // Other Functions
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
            $oldImagePath = public_path($image->file_path);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $image->delete();
        }

        return response()->json(['success' => true]);
    }
}
