<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\MembershipNotifications;
use App\Models\Building;
use App\Models\BuildingUnit;
use App\Models\ManagerBuilding;
use App\Models\Membership;
use App\Models\MembershipUser;
use App\Models\PlanSubscriptionItem;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user() ?? abort(404, 'Page not found');

        try {
            $memberships = collect();
            $buildings = collect();
            $units = collect();
            $types = ['Restaurant', 'Gym', 'Other'];
            $statuses = ['Draft', 'Published', 'Non Renewable', 'Archived'];

            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.Memberships.index', compact('memberships', 'buildings', 'units', 'types', 'statuses'));
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $membershipQuery = Membership::where('organization_id', $organization_id)
                ->with([
                    'unit:id,unit_name',
                    'building:id,name'
                ]);

            $buildingsQuery = Building::where('organization_id', $organization_id)
                ->whereIn('status', ['Approved', 'For Re-Approval'])
                ->where('isFreeze', 0)
                ->select('id', 'name');

            $unitsQuery = BuildingUnit::where('organization_id', $organization_id)
                ->where('status', 'Approved')
                ->where('availability_status', 'Available')
                ->where('sale_or_rent', 'Not Available')
                ->whereNotIn('unit_type', ['Room', 'Shop', 'Apartment'])
                ->select('id', 'unit_name');

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id');

                if ($managerBuildingIds->isEmpty()) {
                    $memberships = collect();
                    return view('Heights.Owner.Memberships.index', compact('memberships', 'buildings', 'units', 'types', 'statuses'));
                }

                $membershipQuery->whereIn('building_id', $managerBuildingIds);
                $buildingsQuery->whereIn('id', $managerBuildingIds);
                $unitsQuery->whereIn('building_id', $managerBuildingIds);
            }

            if ($request->filled('building_id')) {
                $membershipQuery->where('building_id', $request->input('building_id'));
            }

            if ($request->filled('unit_id')) {
                $membershipQuery->where('unit_id', $request->input('unit_id'));
            }

            if ($request->filled('type') && in_array($request->input('type'), $types)) {
                $membershipQuery->where('category', $request->input('type'));
            }

            if ($request->filled('status') && in_array($request->input('status'), $statuses)) {
                $membershipQuery->where('status', $request->input('status'));
            }

            if ($request->filled('min_price')) {
                $membershipQuery->where('price', '>=', $request->input('min_price'));
            }

            if ($request->filled('max_price')) {
                $membershipQuery->where('price', '<=', $request->input('max_price'));
            }

            if ($request->has('featured') && in_array($request->featured, ['0', '1'])) {
                $membershipQuery->where('mark_as_featured', $request->featured);
            }

            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $membershipQuery->where('name', 'like', '%' . $searchTerm . '%');
            }

            $memberships = $membershipQuery->paginate(12);
            $buildings = $buildingsQuery->get();
            $units = $unitsQuery->get();

            return view('Heights.Owner.Memberships.index', compact('memberships', 'buildings', 'units', 'types', 'statuses'));

        } catch (\Throwable $e) {
            Log::error('Error in Memberships index: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function create(Request $request)
    {
        $user = $request->user() ?? abort(404, 'Page not found');

        try {
            $buildings = collect();
            $types = ['Restaurant', 'Gym', 'Other'];
            $statuses = ['Draft', 'Published', 'Non Renewable'];
            $currency = ['PKR'];

            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.Memberships.create', compact('buildings', 'types', 'statuses', 'currency'));
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $buildingsQuery = Building::where('organization_id', $organization_id)
                ->whereIn('status', ['Approved', 'For Re-Approval'])
                ->where('isFreeze', 0)
                ->select('id', 'name');

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id');

                if ($managerBuildingIds->isEmpty()) {
                    return view('Heights.Owner.Memberships.create', compact('buildings', 'types', 'statuses', 'currency'));
                }

                $buildingsQuery->whereIn('id', $managerBuildingIds);
            }

            $buildings = $buildingsQuery->get();

            return view('Heights.Owner.Memberships.create', compact('buildings','types', 'statuses', 'currency'));

        } catch (\Throwable $e) {
            Log::error('Error in Memberships create: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function store(Request $request)
    {
        $user = request()->user() ?? abort(404, 'Unauthorized Action');

        $request->validate([
            'unit_id' => 'required|exists:buildingunits,id',
            'building_id' => 'required|exists:buildings,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => ['required', 'string', 'max:100',
                Rule::unique('memberships')->where(function ($query) use ($request) {
                    return $query->where('unit_id', $request->unit_id);
                }),
            ],
            'url' => 'required|url|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:Gym,Restaurant,Other',
            'duration_months' => 'required|integer|min:1',
            'scans_per_day' => 'required|integer|min:1',
            'currency' => 'nullable|string|max:10',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'status' => 'in:Draft,Published,Non Renewable,Archived',
        ]);

        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->withInput()->with('error', 'Only organization related personals can perform this action.');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            if ($role_name === 'Manager' && !ManagerBuilding::where('building_id', $request->building_id)
                    ->where('user_id', $user->id)
                    ->exists()) {
                return redirect()->back()->withInput()->with('error', 'You do not have access to add memberships for the selected building.');
            }

            $unit = BuildingUnit::where('id', $request->unit_id)
                ->where('organization_id', $organization_id)->first();

            if (!$unit || $unit->building_id !== (int)$request->building_id) {
                return redirect()->back()->withInput()->with('error', 'Selected unit does not belong to the selected building.');
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $this->handleImageUpload($request);
            }

            $membership = Membership::create([
                'organization_id' => $organization_id,
                'unit_id' => $request->unit_id,
                'building_id' => $request->building_id,
                'image' => $imagePath ?? 'uploads/memberships/images/defaultImage.jpeg',
                'name' => $request->name,
                'url' => $request->url,
                'description' => $request->description,
                'category' => $request->category,
                'duration_months' => $request->duration_months,
                'scans_per_day' => $request->scans_per_day,
                'currency' => $request->currency,
                'price' => $request->price,
                'original_price' => $request->original_price,
                'status' => $request->status,
            ]);

            dispatch(new MembershipNotifications(
                $organization_id,
                $membership->id,
                "New membership Created by {$token['role_name']} ({$user->name})",
                "The membership '{$membership->name}' has been successfully created.",
                "owner/memberships/{$membership->id}/show",

                $user->id,
                "New Membership Created",
                "The membership '{$membership->name}' has been successfully created.",
                "owner/memberships/{$membership->id}/show",
            ));

            return redirect()->route('owner.memberships.index')->with('success', 'Membership created successfully.');

        } catch (\Throwable $e) {
            Log::error('Error in Memberships store: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function edit(Request $request, $id)
    {
        $user = $request->user() ?? abort(404, 'Page not found');

        try {
            $types = ['Restaurant', 'Gym', 'Other'];
            $currency = ['PKR'];
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->with('error', 'Only organization-related personnel can perform this action.');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $buildingsQuery = Building::where('organization_id', $organization_id)
                ->whereIn('status', ['Approved', 'For Re-Approval'])
                ->where('isFreeze', 0)
                ->select('id', 'name');

            $membership = Membership::where('id', $id)
                ->where('organization_id', $organization_id)
                ->with([
                    'unit:id,unit_name',
                    'building:id,name'
                ])->first();

            if (!$membership) {
                return redirect()->back()->with('error', 'Membership not found.');
            }

            $statuses = ['Published', 'Non Renewable', $membership->status === 'Draft' ? 'Draft' : 'Archived'];

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();

                if (!in_array($membership->building_id, $managerBuildingIds)) {
                    return redirect()->back()->with('error', 'You do not have access to edit this membership.');
                }

                $buildingsQuery->whereIn('id', $managerBuildingIds);
            }

            $buildings = $buildingsQuery->get();

            return view('Heights.Owner.Memberships.edit', compact(
                'membership', 'buildings', 'types', 'statuses', 'currency'
            ));

        } catch (\Throwable $e) {
            Log::error('Error in Memberships edit: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function update(Request $request)
    {
        $user = $request->user() ?? abort(404, 'Unauthorized Action');

        $request->validate([
            'id' => 'required|exists:memberships,id',
            'unit_id' => 'required|exists:buildingunits,id',
            'building_id' => 'required|exists:buildings,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => ['required', 'string', 'max:100',
                Rule::unique('memberships')->ignore($request->id)->where(function ($query) use ($request) {
                    return $query->where('unit_id', $request->unit_id);
                }),
            ],
            'url' => 'required|url|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:Gym,Restaurant,Other',
            'duration_months' => 'required|integer|min:1',
            'scans_per_day' => 'required|integer|min:1',
            'currency' => 'nullable|string|max:10',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'status' => 'in:Draft,Published,Non Renewable,Archived',
        ]);

        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->withInput()->with('error', 'Only organization-related personnel can perform this action.');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $membership = Membership::where('id', $request->id)
                ->where('organization_id', $organization_id)
                ->first();

            if (!$membership) {
                return redirect()->back()->with('error', 'Membership not found.');
            }

            if ($role_name === 'Manager' && !ManagerBuilding::where('building_id', $request->building_id)
                    ->where('user_id', $user->id)->exists()) {
                return redirect()->back()->withInput()->with('error', 'You do not have access to edit memberships for the selected building.');
            }

            $unit = BuildingUnit::where('id', $request->unit_id)
                ->where('organization_id', $organization_id)->first();

            if (!$unit || $unit->building_id !== (int)$request->building_id) {
                return redirect()->back()->withInput()->with('error', 'Selected unit does not belong to the selected building.');
            }

            $imagePath = $membership->image;
            if ($request->hasFile('image')) {
                $imagePath = $this->handleImageUpload($request);
            }

            $membership->update([
                'unit_id' => $request->unit_id,
                'building_id' => $request->building_id,
                'image' => $imagePath,
                'name' => $request->name,
                'url' => $request->url,
                'description' => $request->description,
                'category' => $request->category,
                'duration_months' => $request->duration_months,
                'scans_per_day' => $request->scans_per_day,
                'currency' => $request->currency,
                'price' => $request->price,
                'original_price' => $request->original_price,
                'status' => $request->status,
            ]);

            if (
                $request->status === 'Archived' &&
                $membership->mark_as_featured
            ) {
                $membership->mark_as_featured = false;
                $membership->save();

                $planItem = PlanSubscriptionItem::where('organization_id', $organization_id)
                    ->where('service_catalog_id', 6)
                    ->lockForUpdate()
                    ->first();

                if ($planItem && $planItem->used > 0) {
                    $planItem->decrement('used');
                }
            }

            dispatch(new MembershipNotifications(
                $organization_id,
                $membership->id,
                "Membership Updated by {$token['role_name']} ({$user->name})",
                "The membership '{$membership->name}' has been successfully updated.",
                "owner/memberships/{$membership->id}/show",

                $user->id,
                "Membership Updated",
                "The membership '{$membership->name}' has been successfully updated.",
                "owner/memberships/{$membership->id}/show",
            ));

            return redirect()->route('owner.memberships.index')->with('success', 'Membership updated successfully.');

        } catch (\Throwable $e) {
            Log::error('Error in Memberships update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function show(Request $request, $id)
    {
        $user = $request->user() ?? abort(404, 'Unauthorized');

        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->with('error', 'Only organization-related personnel can perform this action.');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $membership = Membership::where('id', $id)
                ->where('organization_id', $organization_id)
                ->with([
                    'unit:id,unit_name,building_id',
                    'building:id,name',
                    'membershipUsers',
                    'membershipUsers.users',
                ])
                ->first();

            if (!$membership) {
                return redirect()->back()->with('error', 'Membership not found.');
            }

            if (
                $role_name === 'Manager' &&
                !ManagerBuilding::where('user_id', $user->id)
                    ->where('building_id', $membership->building_id)
                    ->exists()
            ) {
                return redirect()->back()->with('error', 'You do not have permission to view this membership.');
            }

            return view('Heights.Owner.Memberships.show', compact('membership'));

        } catch (\Throwable $e) {
            Log::error('Error in Memberships show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function assignMembershipView(Request $request, $id)
    {
        $user = $request->user() ?? abort(404, 'Unauthorized');

        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->with('error', 'Only organization-related personnel can perform this action.');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $membership = Membership::where('id', $id)
                ->where('organization_id', $organization_id)
                ->where('status', '!=', 'Archived')
                ->with(['building:id,name', 'unit:id,unit_name'])
                ->first();

            if (!$membership) {
                return redirect()->back()->with('error', 'Membership not found.');
            }

            if (
                $role_name === 'Manager' &&
                !ManagerBuilding::where('user_id', $user->id)
                    ->where('building_id', $membership->building_id)
                    ->exists()
            ) {
                return redirect()->back()->with('error', 'You do not have permission to assign this membership.');
            }

            $assignedUserIds = MembershipUser::where('membership_id', $id)
                ->where('status', 1)
                ->pluck('user_id');

            $availableUsers = User::where('organization_id', $organization_id)
                ->where('id', '!=', $user->id)
                ->whereNotIn('id', $assignedUserIds)
                ->select('id', 'name', 'email', 'cnic', 'picture')
                ->get();

            return view('Heights.Owner.Memberships.assign', compact('membership', 'availableUsers'));

        } catch (\Throwable $e) {
            Log::error('Error in assignMembershipView: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function assignMembership(Request $request)
    {
        $loggedUser = $request->user() ?? abort(404, 'Unauthorized');

        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Only organization-related personnel can perform this action.');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $membership = Membership::where('id', $request->membership_id)
                ->where('status', '!=', 'Archived')
                ->where('organization_id', $organization_id)
                ->first();

            if (!$membership) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Membership not found.');
            }

            if (
                $role_name === 'Manager' &&
                !ManagerBuilding::where('user_id', $loggedUser->id)
                    ->where('building_id', $membership->building_id)
                    ->exists()
            ) {
                DB::rollBack();
                return redirect()->back()->with('error', 'You do not have permission to assign this membership.');
            }

            $alreadyAssigned = MembershipUser::where('membership_id', $membership->id)
                ->where('user_id', $request->user_id)
                ->where('status', 1)
                ->exists();

            if ($alreadyAssigned) {
                DB::rollBack();
                return redirect()->back()->with('error', 'This membership is already assigned to the selected user.');
            }

            $user = User::find($request->user_id);

            if(!$user){
                DB::rollBack();
                return redirect()->back()->with('error', 'Selected User is invalid.');
            }

            $transaction = $this->membershipAssignment_Transaction($user, $membership, null);

            DB::commit();

            $this->sendMembershipSuccessNotifications($membership->organization_id, $membership, $transaction, $user, $loggedUser);

            return redirect()->route('owner.memberships.index')->with('success', 'Membership assigned to user successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error assigning membership: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function toggleFeatured(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
        ]);

        DB::beginTransaction();

        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Only organization-related personnel can perform this action.');
            }

            $organization_id = $token['organization_id'];

            $membership = Membership::where('id', $request->membership_id)
                ->where('organization_id', $organization_id)
                ->lockForUpdate()
                ->first();

            if (!$membership) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Membership not found.');
            }

            if (in_array($membership->status, ['Archived', 'Draft'])) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Archived or Draft memberships cannot be toggled as featured.');
            }

            $subscriptionLimit = PlanSubscriptionItem::where('organization_id', $organization_id)
                ->where('service_catalog_id', 6)
                ->lockForUpdate()
                ->first();

            if (!$subscriptionLimit) {
                DB::rollBack();
                return redirect()->back()->with('plan_upgrade_error', 'This plan does not support featured memberships.');
            }

            if (!$membership->mark_as_featured) {
                if ($subscriptionLimit->used >= $subscriptionLimit->quantity) {
                    DB::rollBack();
                    return redirect()->back()->with('plan_upgrade_error', 'Featured membership limit reached. Upgrade your plan.');
                }

                $membership->mark_as_featured = true;
                $subscriptionLimit->increment('used');

                DB::commit();
                return redirect()->back()->with('success', 'Membership marked as featured successfully.');
            } else {
                $membership->mark_as_featured = false;

                if ($subscriptionLimit->used > 0) {
                    $subscriptionLimit->decrement('used');
                }

                DB::commit();
                return redirect()->back()->with('success', 'Membership unmarked from featured successfully.');
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in markedAsFeatured (toggle): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }



    // Helper Functions
    private function handleImageUpload(Request $request): string
    {
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = 'uploads/memberships/images/' . $imageName;
        $image->move(public_path('uploads/memberships/images'), $imageName);
        return $imagePath;
    }

    private function membershipAssignment_Transaction($user, $membership, $paymentIntentId)
    {
        $source_id = $membership->id;
        $source_name = 'membership';
        $isSubscription = false;

        if ($membership->status === 'Published') {
            $subscription = Subscription::create([
                'customer_payment_id' => $user->customer_payment_id,
                'building_id' => $membership->building_id,
                'unit_id' => $membership->unit_id,
                'user_id' => $user->id,
                'organization_id' => $membership->organization_id,
                'source_id' => $source_id,
                'source_name' => $source_name,
                'billing_cycle' => $membership->duration_months,
                'subscription_status' => 'Active',
                'price_at_subscription' => $membership->price,
                'currency_at_subscription' => $membership->currency,
                'ends_at' => now()->addMonths($membership->duration_months),
            ]);

            $source_id = $subscription->id;
            $source_name = 'subscription';
            $isSubscription = true;
        }

        MembershipUser::create([
            'user_id' => $user->id,
            'membership_id' => $membership->id,
            'subscription_id' => $isSubscription ? $source_id : null,
            'quantity' => $membership->scans_per_day,
            'used' => $membership->scans_per_day,
        ]);

        return Transaction::create([
            'transaction_title' => "{$membership->name}",
            'transaction_category' => 'New',
            'building_id' => $membership->building_id,
            'unit_id' => $membership->unit_id,
            'buyer_id' => $user->id,
            'buyer_type' => 'user',
            'seller_type' => 'organization',
            'seller_id' => $membership->organization_id,
            'payment_method' => 'Card',
            'gateway_payment_id' => $paymentIntentId,
            'price' => $membership->price,
            'currency' => $membership->currency,
            'status' => 'Completed',
            'is_subscription' => $isSubscription,
            'billing_cycle' => $isSubscription ? "{$membership->duration_months} Month" : null,
            'subscription_start_date' => $isSubscription ? now() : null,
            'subscription_end_date' => $isSubscription ? now()->addMonths($membership->duration_months) : null,
            'source_id' => $source_id,
            'source_name' => $source_name,
        ]);
    }

    private function sendMembershipSuccessNotifications($organizationId, $membership, $transaction, $user, $loggedUser)
    {
        $userId = $user->id;
        $billingCycle = $membership->duration_months ?? 1;
        $price = $transaction->price ?? $membership->price;

        $userHeading = "{$membership->name} Purchased Successfully!";
        $userMessage = "Congratulations! You have successfully purchased the {$membership->name} "
            . "for the price of {$price} PKR"
            . ($membership->status === 'Non Renewable' ? '.' : " per {$billingCycle} month(s).");


        $ownerHeading = "{$membership->name} sold successfully";
        $ownerMessage = "{$membership->name} has been sold successfully for Price: {$price}";


        $transactionHeading = "Transaction Completed Successfully";
        $transactionMessage = "A payment of {$price} PKR has been successfully recorded for the sale of {$membership->name}.";

        $userTransactionHeading = "Transaction Successful!";
        $userTransactionMessage = "You have successfully made a payment of {$price} PKR for {$membership->name}.";


        dispatch(new MembershipNotifications(
            $organizationId,
            $membership->id,
            $ownerHeading,
            $ownerMessage,
            "owner/memberships/{$membership->id}/show",

            $loggedUser->id,
            $ownerHeading,
            $ownerMessage,
            "owner/memberships/{$membership->id}/show",

            $userId,
            $userHeading,
            $userMessage,
            "",
        ));


        dispatch(new MembershipNotifications(
            $organizationId,
            $membership->id,
            $transactionHeading,
            $transactionMessage,
            "owner/finance/{$transaction->id}/show",

            $loggedUser->id,
            $transactionHeading,
            $transactionMessage,
            "owner/finance/{$transaction->id}/show",

            $userId,
            $userTransactionHeading,
            $userTransactionMessage,
            "",
        ));
    }

}
