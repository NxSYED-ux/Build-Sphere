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
use App\Models\StaffMember;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MembershipService;
use App\Services\OwnerFiltersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class MembershipController extends Controller
{
    public function index(Request $request)
    {
        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->approvedBuildings($buildingIds);
            $units = $ownerService->membershipsUnits($buildingIds);
            $types = ['Restaurant', 'Gym', 'Other'];
            $statuses = ['Draft', 'Published', 'Non Renewable', 'Archived'];

            $membershipQuery = Membership::where('organization_id', $organization_id)
                ->whereIn('building_id', $buildingIds)
                ->with([
                    'unit:id,unit_name',
                    'building:id,name'
                ]);

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

            $memberships = $membershipQuery->orderBy('updated_at', 'desc')->paginate(12);

            return view('Heights.Owner.Memberships.index', compact('memberships', 'buildings', 'units', 'types', 'statuses'));

        } catch (\Throwable $e) {
            Log::error('Error in Memberships index: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function create()
    {
        try {
            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->approvedBuildings($buildingIds);
            $types = ['Restaurant', 'Gym', 'Other'];
            $statuses = ['Draft', 'Published', 'Non Renewable'];
            $currency = ['PKR'];

            return view('Heights.Owner.Memberships.create', compact('buildings','types', 'statuses', 'currency'));

        } catch (\Throwable $e) {
            Log::error('Error in Memberships create: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function store(Request $request)
    {
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
            'currency' => 'required|string|max:10',
            'price' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'status' => 'required|in:Draft,Published,Non Renewable,Archived',
        ] , [
            'name.unique' => 'The membership name has already been taken for this unit.',
        ]);

        try {
            $user = $request->user();
            $token = $request->attributes->get('token');

            $ownerService = new OwnerFiltersService();
            $result = $ownerService->checkBuildingAccess($request->building_id);

            if(!$result['access']){
                return redirect()->back()->withInput()->with('error', $result['message']);
            }

            $organization_id = $result['organization_id'];

            $unit = BuildingUnit::where('id', $request->unit_id)
                ->where('organization_id', $organization_id)->first();

            if (!$unit || $unit->building_id !== (int) $request->building_id) {
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
                'offered_discount' => $request->discount,
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
        try {
            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->approvedBuildings($buildingIds);
            $types = ['Restaurant', 'Gym', 'Other'];
            $currency = ['PKR'];

            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $membership = Membership::where('id', $id)
                ->where('organization_id', $organization_id)
                ->whereIn('building_id', $buildingIds)
                ->with([
                    'unit:id,unit_name',
                    'building:id,name'
                ])->first();

            if (!$membership) {
                return redirect()->back()->with('error', 'Membership not found.');
            }

            $statuses = ['Published', 'Non Renewable', $membership->status === 'Draft' ? 'Draft' : 'Archived'];

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
            'discount' => 'nullable|numeric|min:0',
            'status' => 'in:Draft,Published,Non Renewable,Archived',
        ] , [
            'name.unique' => 'The membership name has already been taken for this unit.',
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();
            $token = $request->attributes->get('token');

            $ownerService = new OwnerFiltersService();
            $result = $ownerService->checkBuildingAccess($request->building_id);

            if(!$result['access']){
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', $result['message']);
            }

            $organization_id = $result['organization_id'];

            $membership = Membership::where('id', $request->id)
                ->where('organization_id', $organization_id)
                ->first();

            if (!$membership) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Membership not found.');
            }

            $unit = BuildingUnit::where('id', $request->unit_id)
                ->where('organization_id', $organization_id)->first();

            if (!$unit || $unit->building_id !== (int) $request->building_id) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Selected unit does not belong to the selected building.');
            }

            $imagePath = $membership->image;
            if ($request->hasFile('image')) {
                $imagePath = $this->handleImageUpload($request);

                if($imagePath && $membership->image){
                    $oldImagePath = public_path($membership->image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }
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
                'offered_discount' => $request->discount,
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

            DB::commit();

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
            DB::rollBack();
            Log::error('Error in Memberships update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $membership = Membership::where('id', $id)
                ->where('organization_id', $organization_id)
                ->with([
                    'unit:id,unit_name,building_id',
                    'building:id,name',
                    'membershipUsers' => function ($query) {
                        $query->where('status', 1);
                    },
                    'membershipUsers.user',
                ])
                ->first();

            if (!$membership) {
                return redirect()->back()->with('error', 'Membership not found.');
            }

            $ownerService = new OwnerFiltersService();
            $result = $ownerService->checkBuildingAccess($membership->building_id);

            if(!$result['access']){
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', $result['message']);
            }

            return view('Heights.Owner.Memberships.show', compact('membership'));

        } catch (\Throwable $e) {
            Log::error('Error in Memberships show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function assignMembershipView(Request $request, $id)
    {
        try {
            $isOwner = request()->user()->id === 2;

            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $membership = Membership::where('id', $id)
                ->where('organization_id', $organization_id)
                ->where('status', '!=', 'Archived')
                ->with(['building:id,name', 'unit:id,unit_name'])
                ->first();

            if (!$membership) {
                return redirect()->back()->with('error', 'Membership not found.');
            }

            $ownerService = new OwnerFiltersService();
            $result = $ownerService->checkBuildingAccess($membership->building_id);

            if(!$result['access']){
                return redirect()->back()->withInput()->with('error', $result['message']);
            }

            $assignedUserIds = MembershipUser::where('membership_id', $id)
                ->where('status', 1)
                ->pluck('user_id')
                ->toArray();

            $availableUsers = $ownerService->users(!$isOwner, $assignedUserIds);

            return view('Heights.Owner.Memberships.assign', compact('membership', 'availableUsers'));

        } catch (\Throwable $e) {
            Log::error('Error in assignMembershipView: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function assignMembership(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $loggedUser = $request->user();
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $membership = Membership::where('id', $request->membership_id)
                ->where('status', '!=', 'Archived')
                ->where('organization_id', $organization_id)
                ->first();

            if (!$membership) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Membership not found.');
            }

            $ownerService = new OwnerFiltersService();
            $result = $ownerService->checkBuildingAccess($membership->building_id);

            if(!$result['access']){
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', $result['message']);
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

            $membershipService = new MembershipService();
            $transaction = $membershipService->membershipAssignment_Transaction($user, $membership);
            $membershipService->sendMembershipSuccessNotifications($membership, $transaction, $user, $loggedUser);

            DB::commit();

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
            'value' => 'required|in:0,1',
        ]);

        DB::beginTransaction();

        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];
            $requestedValue = (bool) $request->value;

            $membership = Membership::where('id', $request->membership_id)
                ->where('organization_id', $organization_id)
                ->lockForUpdate()
                ->first();

            if (!$membership) {
                DB::rollBack();
                return response()->json(['error' => 'Membership not found.'], 404);
            }

            if (in_array($membership->status, ['Archived', 'Draft'])) {
                DB::rollBack();
                return response()->json(['error' => 'Archived or Draft memberships cannot be toggled as featured.'], 400);
            }

            if ((bool) $membership->mark_as_featured === $requestedValue) {
                DB::rollBack();
                return response()->json(['success' => 'Membership is already in the requested state.']);
            }

            $subscriptionLimit = PlanSubscriptionItem::where('organization_id', $organization_id)
                ->where('service_catalog_id', 6)
                ->lockForUpdate()
                ->first();

            if (!$subscriptionLimit) {
                DB::rollBack();
                return response()->json(['error' => 'This plan does not support featured memberships.'], 400);
            }

            if ($requestedValue) {
                if ($subscriptionLimit->used >= $subscriptionLimit->quantity) {
                    DB::rollBack();
                    return response()->json(['error' => 'Featured membership limit reached. Upgrade your plan.'], 400);
                }

                $membership->mark_as_featured = 1;
                $membership->save();

                $subscriptionLimit->increment('used');

                DB::commit();

                return response()->json(['success' => 'Membership marked as featured successfully.']);

            } else {
                $membership->mark_as_featured = 0;
                $membership->save();

                if ($subscriptionLimit->used > 0) {
                    $subscriptionLimit->decrement('used');
                }

                DB::commit();

                return response()->json(['success' => 'Membership unmarked from featured successfully.']);
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in toggleFeatured: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong! Please try again.'], 500);
        }
    }


    public function markAsPaymentReceived(Request $request)
    {
        $request->validate([
            'user_membership_id' => 'required|exists:membership_users,id',
        ]);

        $loggedUser = request()->user();

        DB::beginTransaction();

        try {
            $membershipUser = MembershipUser::with('membership', 'user')->findOrFail($request->user_membership_id);
            $user = $membershipUser->user;
            $membership = $membershipUser->membership;

            $existingSubscription = Subscription::find($membershipUser->subscription_id);

            $membershipService = new MembershipService();
            $transaction = $membershipService->membershipAssignment_Transaction(
                $user,
                $membership,
                null,
                'Cash',
                'renewal',
                $membershipUser,
                $existingSubscription
            );

            $membershipService->sendMembershipSuccessNotifications($membership, $transaction, $user, $loggedUser);

            DB::commit();

            return response()->json([
                'message' => 'Payment marked as received and transaction recorded.',
                'transaction' => $transaction,
            ]);

        } catch (\Throwable $e) {
            DB::rollback();
            Log::error('Membership payment failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to mark payment as received.',
                'error' => $e->getMessage(),
            ], 500);
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

}
