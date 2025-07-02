<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\BuildingNotifications;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\BuildingUnit;
use App\Models\DropdownType;
use App\Models\ManagerBuilding;
use App\Models\Organization;
use App\Models\PlanSubscriptionItem;
use App\Models\UnitPicture;
use App\Models\UserBuildingUnit;
use App\Services\AdminFiltersService;
use App\Services\OwnerFiltersService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Monolog\Level;

class BuildingUnitController extends Controller
{
    // Index
    public function adminIndex(Request $request)
    {
        try {
            $adminService = new AdminFiltersService();
            $allowedStatusesForBuilding = $adminService->getAllowedStatusesForBuilding();

            $search = $request->input('search');
            $selectedOrganization = $request->input('organization_id');
            $selectedBuildingId = $request->input('building_id');
            $selectedLevelId = $request->input('level_id');
            $selectedStatus = $request->input('status');

            $unitsQuery = BuildingUnit::with(['level', 'building', 'organization', 'pictures'])
                ->whereHas('building', function ($query) use ($allowedStatusesForBuilding) {
                    $query->whereIn('status', $allowedStatusesForBuilding);
                });

            if(!empty($search)) {
                $unitsQuery->where(function ($query) use ($search) {
                    $query->where('unit_name', 'LIKE', "%{$search}%");
                });
            }

            if ($selectedOrganization) {
                $unitsQuery->where('organization_id', $selectedOrganization);
            }

            if ($selectedBuildingId) {
                $unitsQuery->where('building_id', $selectedBuildingId);
            }

            if ($selectedLevelId) {
                $unitsQuery->where('level_id', $selectedLevelId);
            }

            if ($selectedStatus) {
                $unitsQuery->where('status', $selectedStatus);
            }

            $units = $unitsQuery->paginate(12);

            $organizations = $adminService->organizations();
            $buildings = $adminService->buildings();
            $levels = $adminService->levels();
            $statuses = ['Approved', 'Rejected'];


            return view('Heights.Admin.Units.index', compact('units', 'organizations', 'buildings', 'levels', 'statuses'));

        } catch (\Throwable $e) {
            Log::error('Error fetching admin units: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function ownerIndex(Request $request)
    {
        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildings = $ownerService->buildings($buildingIds);
            $levels = $ownerService->levels($buildingIds);
            $statuses = ['Approved', 'Rejected'];

            $search = $request->input('search');
            $selectedBuildingId = $request->input('building_id');
            $selectedLevelId = $request->input('level_id');
            $selectedStatus = $request->input('status');

            $unitsQuery = BuildingUnit::with(['level', 'building', 'organization', 'pictures'])
                ->where('organization_id', $organization_id)
                ->whereIn('building_id', $buildingIds);

            if (!empty($search)) {
                $unitsQuery->where(function ($query) use ($search) {
                    $query->where('unit_name', 'LIKE', "%{$search}%");
                });
            }

            if ($selectedBuildingId) {
                $unitsQuery->where('building_id', $selectedBuildingId);
            }

            if ($selectedLevelId) {
                $unitsQuery->where('level_id', $selectedLevelId);
            }

            if ($selectedStatus) {
                $unitsQuery->where('status', $selectedStatus);
            }

            $units = $unitsQuery->paginate(12);

            return view('Heights.Owner.Units.index', compact('units', 'buildings', 'levels', 'statuses'));

        } catch (\Throwable $e) {
            Log::error('Error in ownerIndex: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    // Create
    public function adminCreate()
    {
        try {
            $adminService = new AdminFiltersService();
            $organizations = $adminService->organizations();
            $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
            $unitTypes = $unitType ? $unitType->values : collect();

            return view('Heights.Admin.Units.create', compact('organizations', 'unitTypes'));

        } catch (\Throwable $e) {
            Log::error('Error in adminCreate: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function ownerCreate()
    {
        try {

            $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
            $unitTypes = $unitType ? $unitType->values : collect();
            $buildings = $this->getBuildingsOwner();

            return view('Heights.Owner.Units.create', compact('buildings', 'unitTypes'));

        } catch (\Throwable $e) {
            Log::error('Error in ownerCreate: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    // Store
    public function adminStore(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);
        return $this->store($request, 'admin',$request->organization_id);
    }

    public function ownerStore(Request $request)
    {
        $ownerService = new OwnerFiltersService();
        $result = $ownerService->checkBuildingAccess($request->building_id);

        if (!$result['access']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return $this->store($request, 'owner', $result['organization_id']);
    }

    private function store(Request $request, string $portal, $organization_id)
    {
        $user = $request->user();
        $errorType = ($user->role_id === 2) ? 'plan_upgrade_error' : 'error';
        $token = $request->attributes->get('token');

        $validated = $request->validate([
            'unit_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('buildingunits')->where(function ($query) use ($request) {
                    return $query->where('building_id', $request->building_id);
                }),
            ],
            'unit_type' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'sale_or_rent' => 'required|string',
            'area' => 'nullable|numeric',
            'level_id' => 'required|integer|exists:buildinglevels,id',
            'building_id' => 'required|integer|exists:buildings,id',
            'unit_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'unit_name.unique' => 'This unit name is already in use for the selected building.',
        ]);

        DB::beginTransaction();

        try {
            $unit = BuildingUnit::create([
                'unit_name' => $validated['unit_name'],
                'unit_type' => $validated['unit_type'],
                'price' => $validated['price'],
                'description' => $validated['description'],
                'sale_or_rent' => $validated['sale_or_rent'],
                'area' => $validated['area'],
                'level_id' => $validated['level_id'],
                'building_id' => $validated['building_id'],
                'organization_id' => $organization_id,
                'status' => $portal === 'Admin' ? 'Approved' : 'Rejected',
            ]);

            if ($request->hasFile('unit_pictures')) {
                foreach ($request->file('unit_pictures') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = 'uploads/units/images/' . $imageName;
                    $image->move(public_path('uploads/units/images'), $imageName);

                    UnitPicture::create([
                        'unit_id' => $unit->id,
                        'file_path' => $imagePath,
                        'file_name' => $imageName,
                    ]);
                }
            }

            if ($portal === 'owner') {
                $building = Building::find($unit->building_id);
                if ($building && $building->status === 'Approved') {
                    $building->update([
                        'status' => 'For Re-Approval',
                        'review_submitted_at' => now()
                    ]);
                }
            }

            $subscriptionItem = PlanSubscriptionItem::where('organization_id', $organization_id)
                ->where('service_catalog_id', 5)
                ->lockForUpdate()
                ->first();

            if (!$subscriptionItem) {
                DB::rollBack();
                return redirect()->back()->with($errorType, 'The current plan doesn\'t include unit management. Upgrade the organization plan to add units.')->withInput();
            }

            $meta = $subscriptionItem->meta ?? ['quantity' => 0];
            $quantity = $subscriptionItem->quantity ?? 0;
            $metaQuantity = $meta['quantity'] ?? 0;

            if ($quantity <= 0 || $metaQuantity <= 0) {
                DB::rollBack();
                return redirect()->back()->with($errorType, 'The current plan doesn\'t include unit management. Upgrade the organization plan to add units.')->withInput();
            }

            $currentBuildingUnits = $meta[$unit->building_id]['used'] ?? 0;
            if ($currentBuildingUnits >= $quantity) {
                DB::rollBack();
                return redirect()->back()->with($errorType, 'Target building has reached its unit limit (max ' . $subscriptionItem->quantity . ' units). Upgrade the organization plan to add more units to that building.')->withInput();
            }

            if (!isset($meta[$unit->building_id])) {
                $buildingCount = count(array_filter(array_keys($meta), 'is_int'));
                if ($buildingCount >= $metaQuantity) {
                    DB::rollBack();
                    return redirect()->back()->with($errorType, 'Building limit reached for units. Upgrade the organization plan to add more buildings.')->withInput();
                }
            }

            $meta[$unit->building_id] = [
                'used' => ($meta[$unit->building_id]['used'] ?? 0) + 1
            ];

            $newHighest = max(array_column(array_filter($meta, 'is_array'), 'used')) ?? 0;
            $subscriptionItem->update([
                'used' => $newHighest,
                'meta' => $meta
            ]);

            $route = $portal === 'admin' ? 'units.index' : 'owner.units.index';
            $detailPath = "{$portal}/units/{$unit->id}/show";

            dispatch(new BuildingNotifications(
                $organization_id,
                $validated['building_id'],
                "New Unit Created by " . ($portal === 'admin' ? 'Admin' : "{$token['role_name']} ({$user->name})"),
                "The Unit '{$validated['unit_name']}' has been successfully created" . ($portal === 'admin' ? ' by admin' : ''),
                "owner/units/{$unit->id}/show",
                $user->id,
                "New Unit Created",
                "The Unit '{$validated['unit_name']}' has been successfully created.",
                $detailPath,
                $portal === 'admin'
            ));

            DB::commit();

            return redirect()->route($route)->with('success', 'Unit created successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Unit Creation Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.')->withInput();
        }
    }



    // Show
    public function adminShow($id)
    {
        try {
            $adminService = new AdminFiltersService();
            $allowedStatusesForBuilding = $adminService->getAllowedStatusesForBuilding();

            $unit = BuildingUnit::with(['pictures', 'level', 'building', 'organization'])
                ->where('id', $id)
                ->whereHas('building', function ($query) use ($allowedStatusesForBuilding) {
                    $query->whereIn('status', $allowedStatusesForBuilding);
                })
                ->first();

            return response()->json(['Unit' => $unit]);
        } catch (\Throwable $e) {
            Log::error('Error fetching Unit Data(Admin): ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching unit data.'], 500);
        }
    }

    public function ownerShow($id)
    {
        try {
            $unit = BuildingUnit::with(['pictures', 'level', 'building', 'organization'])->findOrFail($id);

            $ownerService = new OwnerFiltersService();
            $result = $ownerService->checkBuildingAccess($unit->building_id);

            if (!$result['access']) {
                return redirect()->back()->with('error', $result['message']);
            }

            $organization_id = $result['organization_id'];

            if ($unit->organization_id !== $organization_id) {
                return redirect()->back()->with('error', 'Invalid Unit Id.');
            }

            $activeContract = UserBuildingUnit::with(['user', 'subscription'])
                ->where([
                    ['unit_id', $unit->id],
                    ['contract_status', 1]
                ])
                ->first();

            $pastContract = UserBuildingUnit::with(['user', 'subscription'])
                ->where([
                    ['unit_id', $unit->id],
                    ['contract_status', 0]
                ])
                ->latest('updated_at')
                ->first();

            return view('Heights.Owner.Units.show', compact('unit', 'activeContract', 'pastContract'));

        } catch (ModelNotFoundException $e){
            return response()->json(['error' => 'Invalid Unit Id.'], 404);

        } catch (\Throwable $e) {
            Log::error("Error fetching Unit Data (Owner) for ID: $id - " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching unit data.'], 500);
        }
    }


    // Edit
    public function adminEdit($id)
    {
        try {
            $adminService = new AdminFiltersService();
            $allowedStatuses = $adminService->getAllowedStatusesForBuilding();

            $unit = BuildingUnit::where('id', $id)
                ->whereHas('building', function ($query) use ($allowedStatuses) {
                    $query->whereIn('status', $allowedStatuses);
                })
                ->first();

            if(!$unit){
                return redirect()->back()->with('error', 'Invalid unit Id');
            }

            $organizations = Organization::where('id', $unit->organization_id)->get();

            $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
            $unitTypes = $unitType ? $unitType->values : collect();

            return view('Heights.Admin.Units.edit', compact( 'unit', 'organizations', 'unitTypes'));

        } catch (\Throwable $e) {
            Log::error('Error in adminCreate: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function ownerEdit($id)
    {
        try{
            $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
            $unitTypes = $unitType ? $unitType->values : collect();

            $unit = BuildingUnit::find($id);

            if (!$unit) {
                return redirect()->back()->with('error', 'Invalid Unit Id');
            }

            $ownerService = new OwnerFiltersService();
            $result = $ownerService->checkBuildingAccess($unit->building_id);

            if (!$result['access']) {
                return redirect()->back()->with('error', $result['message']);
            }

            $organization_id = $result['organization_id'];

            if ($organization_id !== $unit->organization_id) {
                return redirect()->back()->with('error', 'Invalid Unit Id');
            }

            $buildings = $this->getBuildingsOwner();

            return view('Heights.Owner.Units.edit', compact( 'unit', 'buildings', 'unitTypes'));

        } catch(\Throwable $e){
            Log::error('Error in ownerEdit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    // Update
    public function adminUpdate(Request $request)
    {
        return $this->update($request, 'admin');
    }

    public function ownerUpdate(Request $request)
    {
        $ownerService = new OwnerFiltersService();
        $result = $ownerService->checkBuildingAccess($request->building_id);

        if (!$result['access']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return $this->update($request, 'owner');
    }

    private function update(Request $request, string $portal)
    {
        $user = $request->user();
        $errorType = ($user->role_id === 2) ? 'plan_upgrade_error' : 'error';
        $token = $request->attributes->get('token');

        $validated = $request->validate([
            'unit_id' => 'required|exists:buildingunits,id',
            'unit_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('buildingunits')->where(function ($query) use ($request) {
                    return $query->where('building_id', $request->building_id);
                })->ignore($request->unit_id),
            ],
            'unit_type' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'sale_or_rent' => 'required|string',
            'area' => 'nullable|numeric',
            'level_id' => 'required|integer|exists:buildinglevels,id',
            'building_id' => 'required|integer|exists:buildings,id',
            'updated_at' => 'required',
            'unit_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'unit_name.unique' => 'This unit name is already in use for the selected building.',
        ]);

        DB::beginTransaction();
        try {
            $unit = BuildingUnit::where([
                ['id', '=', $validated['unit_id']],
                ['updated_at', '=', $validated['updated_at']]
            ])->lockForUpdate()->first();

            if (!$unit) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Please refresh the page and try again.')->withInput();
            }

            if ($portal === 'owner' && $token['organization_id'] !== $unit->organization_id) {
                DB::rollBack();
                return redirect()->back()->with('error', 'The selected unit id is invalid.')->withInput();
            }

            $oldBuildingId = $unit->building_id;
            $buildingChanged = ($oldBuildingId != $validated['building_id']);

            $oldLevelId = $unit->level_id;
            $levelChanged = ($oldLevelId != $validated['level_id']);

            if (($buildingChanged || $levelChanged) && $unit->status === 'Approved') {
                DB::rollBack();
                return redirect()->back()->with('error', "Can't change building or level of an Approved unit.")->withInput();
            }

            if ($buildingChanged) {
                $subscriptionItem = PlanSubscriptionItem::where('organization_id', $unit->organization_id)
                    ->where('service_catalog_id', 5)
                    ->lockForUpdate()
                    ->first();

                if (!$subscriptionItem) {
                    DB::rollBack();
                    return redirect()->back()->with($errorType, 'The current plan doesn\'t include unit management. Upgrade the organization plan to add units.')->withInput();
                }

                $meta = $subscriptionItem->meta ?? ['quantity' => 0];
                $quantity = $subscriptionItem->quantity ?? 0;
                $metaQuantity = $meta['quantity'] ?? 0;

                if ($quantity <= 0 || $metaQuantity <= 0) {
                    DB::rollBack();
                    return redirect()->back()->with($errorType, 'The current plan doesn\'t include unit management. Upgrade the organization plan to add units.')->withInput();
                }

                if (isset($meta[$oldBuildingId])) {
                    $meta[$oldBuildingId]['used'] = max(0, $meta[$oldBuildingId]['used'] - 1);
                    if ($meta[$oldBuildingId]['used'] <= 0) {
                        unset($meta[$oldBuildingId]);
                    }
                }

                $buildingCount = count(array_filter(array_keys($meta), 'is_int'));
                if (!isset($meta[$validated['building_id']]) && $buildingCount >= $meta['quantity']) {
                    DB::rollBack();
                    return redirect()->back()->with($errorType, 'Building limit reached. Upgrade the organization plan to add more building..')->withInput();
                }

                $newBuildingUnits = $meta[$validated['building_id']]['used'] ?? 0;
                if ($newBuildingUnits >= $subscriptionItem->quantity) {
                    DB::rollBack();
                    return redirect()->back()->with($errorType, 'Target building has reached its unit limit (max ' . $subscriptionItem->quantity . ' units). Upgrade the organization plan to add more units to that building.')->withInput();
                }

                $meta[$validated['building_id']] = ['used' => $newBuildingUnits + 1];

                $newHighest = max(array_column(array_filter($meta, 'is_array'), 'used')) ?? 0;
                $subscriptionItem->update([
                    'used' => $newHighest,
                    'meta' => $meta
                ]);

                if ($portal === 'owner') {
                    $building = Building::where('id', $unit->building_id)->first();
                    if ($building->status === 'Approved') {
                        $building->update(['status' => 'For Re-Approval']);
                    }
                }
            }

            $unit->update([
                'unit_name' => $validated['unit_name'],
                'unit_type' => $validated['unit_type'],
                'price' => $validated['price'],
                'description' => $validated['description'],
                'sale_or_rent' => $validated['sale_or_rent'],
                'area' => $validated['area'],
                'level_id' => $validated['level_id'],
                'building_id' => $validated['building_id'],
                'updated_at' => now(),
            ]);

            if ($request->hasFile('unit_pictures')) {
                foreach ($request->file('unit_pictures') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = 'uploads/units/images/' . $imageName;
                    $image->move(public_path('uploads/units/images'), $imageName);

                    UnitPicture::create([
                        'unit_id' => $unit->id,
                        'file_path' => $imagePath,
                        'file_name' => $imageName,
                    ]);
                }
            }

            $route = $portal === 'admin' ? 'units.index' : 'owner.units.index';
            $detailPath = "{$portal}/units/{$unit->id}/show";

            dispatch(new BuildingNotifications(
                $unit->organization_id,
                $validated['building_id'],
                "Unit Updated by " . ($portal === 'admin' ? 'Admin' : "{$token['role_name']} ({$user->name})"),
                "The Unit '{$validated['unit_name']}' has been updated" . ($portal === 'admin' ? ' by admin' : ''),
                "owner/units/{$unit->id}/show",
                $user->id,
                "Unit Updated",
                "The Unit '{$validated['unit_name']}' has been updated with your changes.",
                $detailPath,
                $portal === 'admin'
            ));

            DB::commit();
            return redirect()->route($route)->with('success', 'Unit updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Unit Update Failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong please try again later.')->withInput();
        }
    }


    // Owner Tree Function
    public function getUnitDetailsWithActiveContract($id)
    {
        try {
            $unit = BuildingUnit::with([
                'pictures' => function ($query) {
                    $query->select('unit_id', 'file_path');
                },
                'userUnits' => function ($query) {
                    $query->where('contract_status', 1)
                        ->select('id', 'unit_id', 'user_id', 'type', 'created_at', 'subscription_id')
                        ->with([
                            'user:id,name,picture',
                            'subscription:id,created_at,ends_at'
                        ]);
                }
            ])->findOrFail($id);

            $ownerService = new OwnerFiltersService();
            $result = $ownerService->checkBuildingAccess($unit->building_id);

            if (!$result['access']) {
                return redirect()->back()->with('error', $result['message']);
            }

            $organization_id = $result['organization_id'];

            if ($unit->organization_id !== $organization_id) {
                return response()->json(['error' => 'Invalid Unit Id.'], 404);
            }

            return response()->json(['Unit' => $unit]);

        } catch (ModelNotFoundException $e){
            return response()->json(['error' => 'Invalid Unit Id.'], 404);

        } catch (\Throwable $e) {
            Log::error('Error fetching Unit Data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching unit data.'], 500);
        }
    }


    // Admin Tree & Assign Unit Function
    public function unitDetails($id)
    {
        try {
            $unit = BuildingUnit::with(['pictures'])->find($id);
            return response()->json(['Unit' => $unit]);
        } catch (\Throwable $e) {
            Log::error('Error fetching Unit Details: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching unit data.'], 500);
        }
    }


    // Delete Unit Image Function
    public function destroyImage(string $id)
    {
        $image = UnitPicture::findOrFail($id);

        if ($image) {
            $oldImagePath = public_path($image->file_path);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $image->delete();
        }

        return response()->json(['success' => true]);
    }


    // Helper Functions
    private function getBuildingsOwner()
    {
        $ownerService = new OwnerFiltersService();
        $buildingIds = $ownerService->getAccessibleBuildingIds();
        return $ownerService->buildings($buildingIds);
    }


    // Get Building Available Units
    public function getAvailableBuildingUnits($building_id)
    {
        $ownerService = new OwnerFiltersService();
        $units = $ownerService->availableUnitsOfBuilding($building_id);

        return response()->json(['units' => $units]);
    }

    // Get Building Units by type
    public function getBuildingUnitsByType($building_id, $unit_type)
    {
        $ownerService = new OwnerFiltersService();
        $units = $ownerService->specificTypesOfUnitsOfBuilding($building_id, [$unit_type]);

        return response()->json(['units' => $units]);
    }

}
