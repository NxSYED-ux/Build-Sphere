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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BuildingUnitController extends Controller
{
    // Index
    public function adminIndex(Request $request)
    {
        try {
            $search = $request->input('search');
            $levelId = $request->input('level_id');

            $unitsQuery = BuildingUnit::with(['level', 'building', 'organization', 'pictures'])
                ->whereHas('building', function ($query) {
                    $query->whereNotIn('status', ['Under Processing', 'Rejected']);
                });

            if(!empty($search)){
                $unitsQuery->where(function ($query) use ($search) {
                    $query->where('unit_name', 'LIKE', "%{$search}%")
                        ->orWhereHas('level', function ($subQuery) use ($search) {
                            $subQuery->where('level_name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('building', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('organization', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            if ($levelId) {
                $unitsQuery->where('level_id', $levelId);
            }

            $units = $unitsQuery->paginate(10)->appends(['search' => $search]);

            return view('Heights.Admin.Units.index', compact('units', 'search'));

        } catch (\Exception $e) {
            Log::error('Error fetching admin units: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function ownerIndex(Request $request)
    {
        try {
            $user = $request->user() ?? abort(403, 'Unauthorized');
            $token = $request->attributes->get('token');
            $search = $request->input('search');
            $units = null;

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.Units.index', compact('units', 'search'));
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];
            $levelId = $request->input('level_id');

            $unitsQuery = BuildingUnit::with(['level', 'building', 'organization', 'pictures'])
                ->where('organization_id', $organization_id);

            if (!empty($search)) {
                $unitsQuery->where(function ($query) use ($search) {
                    $query->where('unit_name', 'LIKE', "%{$search}%")
                        ->orWhereHas('level', function ($subQuery) use ($search) {
                            $subQuery->where('level_name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('building', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('organization', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
                $unitsQuery->whereIn('building_id', $managerBuildingIds);
            }

            if ($levelId) {
                $unitsQuery->where('level_id', $levelId);
            }

            $units = $unitsQuery->paginate(10)->appends(['search' => $search]);

            return view('Heights.Owner.Units.index', compact('units', 'search'));

        } catch (\Exception $e) {
            Log::error('Error in ownerIndex: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    // Create
    public function adminCreate()
    {
        try {
            $organizations = Organization::where('status', 'Enable')->get();
            $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
            $unitTypes = $unitType ? $unitType->values : collect();

            return view('Heights.Admin.Units.create', compact('organizations', 'unitTypes'));

        } catch (\Exception $e) {
            Log::error('Error in adminCreate: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function ownerCreate(Request $request)
    {
        try {
            $buildings = collect();
            $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
            $unitTypes = $unitType ? $unitType->values : collect();

            $user = $request->user() ?? abort(403, 'Unauthorized');
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.Units.create', compact('buildings', 'unitTypes'));
            }

            $role_name = $token['role_name'];
            $organization_id = $token['organization_id'];

            $buildings = $this->getBuildingsOwner($organization_id, $role_name, $user->id);

            return view('Heights.Owner.Units.create', compact('buildings', 'unitTypes'));

        } catch (\Exception $e) {
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
        return $this->store($request, 'admin',$request->organization_id,'Approved');
    }

    public function ownerStore(Request $request)
    {
        $organization_id = $this->validateOwnerAccess($request,'You do not have access to add units of the selected building.');
        if ($organization_id instanceof RedirectResponse) {
            return $organization_id;
        }
        return $this->store($request, 'owner',$organization_id,'Rejected');
    }

    private function store(Request $request, string $portal, $organization_id, $status)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
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

        if ($portal === 'owner' && $token['organization_id'] !== $organization_id) {
            return redirect()->back()->withInput()->with('error', 'You cannot perform this action.');
        }

        try {
            return DB::transaction(function () use ($request, $portal, $organization_id, $user, $token, $validated, $status) {

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
                    'status' => $status,
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

                if($portal === 'owner'){
                    $building = Building::where('id', $unit->building_id)->first();

                    if($building->status === 'Approved') {
                        $building->update([
                            'status' => 'For Re-Approval',
                        ]);
                    }
                }

                $subscriptionItem = PlanSubscriptionItem::where('organization_id', $organization_id)
                    ->where('service_catalog_id', 5)
                    ->lockForUpdate()
                    ->first();

                if (!$subscriptionItem) {
                    throw new \Exception('The current plan doesn\'t include unit management.');
                }

                $meta = $subscriptionItem->meta ?? ['quantity' => 0];

                if ($subscriptionItem->quantity <= 0 || $meta['quantity'] <= 0) {
                    throw new \Exception('The current plan doesn\'t include unit management. Please upgrade the plan.');
                }

                $currentBuildingUnits = $meta[$unit->building_id]['used'] ?? 0;
                if ($currentBuildingUnits >= $subscriptionItem->quantity) {
                    throw new \Exception('This building has reached its unit limit (max ' . $subscriptionItem->quantity .' units).');
                }

                if (!isset($meta[$unit->building_id])) {
                    $buildingCount = count(array_filter(array_keys($meta), 'is_int'));
                    if ($buildingCount >= $meta['quantity']) {
                        throw new \Exception('Building limit reached for units. Upgrade the organization plan to add more buildings.');
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

                return redirect()->route($route)->with('success', 'Unit created successfully.');
            });
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Unit Creation Failed: ' . $e->getMessage());

            $errorType = $user->role_id === 2 ? 'plan_upgrade_error' : 'error';
            return redirect()->back()->with($errorType, $e->getMessage())->withInput();
        }
    }


    //Show
    public function adminShow($id)
    {
        try {
            $unit = BuildingUnit::with(['pictures', 'level', 'building', 'organization'])
                ->where('id', $id)
                ->whereHas('building', function ($query) {
                    $query->whereNotIn('status', ['Under Processing', 'Rejected']);
                })
                ->first();

            return response()->json(['Unit' => $unit]);
        } catch (\Exception $e) {
            Log::error('Error fetching Unit Data(Admin): ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching unit data.'], 500);
        }
    }

    public function ownerShow(Request $request, $id)
    {
        try {
            $unit = BuildingUnit::with(['pictures', 'level', 'building', 'organization'])->find($id);

            $organization_id = $this->validateOwnerAccess($request,'You do not have access to view units of the selected building.',false, $unit?->building_id);
            if ($organization_id instanceof RedirectResponse) {
                return $organization_id;
            }

            if (!$unit || $unit->organization_id !== $organization_id) {
                return redirect()->back()->with('error', 'Invalid Unit Id');
            }

            $activeContract = UserBuildingUnit::with(['user', 'updater', 'pictures'])
                ->where([
                    ['unit_id', $unit->id],
                    ['contract_status', 1]
                ])
                ->first();

            $pastContract = UserBuildingUnit::with(['user', 'updater', 'pictures'])
                ->where([
                    ['unit_id', $unit->id],
                    ['contract_status', 0]
                ])
                ->latest('updated_at')
                ->first();

            return view('Heights.Owner.Units.show', compact('unit', 'activeContract', 'pastContract'));

        } catch (\Exception $e) {
            Log::error("Error fetching Unit Data (Owner) for ID: $id - " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching unit data.'], 500);
        }
    }


    // Edit
    public function adminEdit($id)
    {
        try {
            $allowedStatuses = ['Approved', 'For Re-approval', 'Under Review'];

            $unit = BuildingUnit::where('id', $id)
                ->whereHas('building', function ($query) use ($allowedStatuses) {
                    $query->whereIn('status', $allowedStatuses);
                })
                ->first();

            if(!$unit){
                return redirect()->back()->with('error', 'Invalid unit Id');
            }

            $organizations = Organization::where('status', 'Enable')
                ->orWhere('id', $unit->organization_id)
                ->get();

            $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
            $unitTypes = $unitType ? $unitType->values : collect();

            return view('Heights.Admin.Units.edit', compact( 'unit', 'organizations', 'unitTypes'));

        } catch (\Exception $e) {
            Log::error('Error in adminCreate: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function ownerEdit(Request $request, $id)
    {
        try{
            $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
            $unitTypes = $unitType ? $unitType->values : collect();

            $user = $request->user() ?? abort(403, 'Unauthorized');
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->with('error', 'Invalid Unit Id');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $unit = BuildingUnit::find($id);
            if (!$unit || $organization_id !== $unit->organization_id) {
                return redirect()->back()->with('error', 'Invalid Unit Id');
            }

            $buildings = $this->getBuildingsOwner($organization_id, $role_name, $user->id);

            return view('Heights.Owner.Units.edit', compact( 'unit', 'buildings', 'unitTypes'));

        }catch(\Exception $e){
            Log::error('Error in ownerEdit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    // Update
    public function adminUpdate(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);
        return $this->update($request, 'admin',$request->organization_id,'Approved');
    }

    public function ownerUpdate(Request $request)
    {
        $organization_id = $this->validateOwnerAccess($request,'You do not have access to update units of the selected building.');
        if ($organization_id instanceof RedirectResponse) {
            return $organization_id;
        }

        return $this->update($request, 'owner', $organization_id,'Rejected');
    }

    private function update(Request $request, string $portal, $organization_id, $status)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
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

        try {
            return DB::transaction(function () use ($request, $portal, $organization_id, $user, $token, $validated, $status) {
                $unit = BuildingUnit::where([
                    ['id', '=', $validated['unit_id']],
                    ['updated_at', '=', $validated['updated_at']]
                ])->lockForUpdate()->first();

                if (!$unit) {
                    throw new \Exception('Please refresh the page and try again.');
                }

                if($portal === 'owner'){
                    $building = Building::where('id', $unit->building_id)->first();

                    if($building->status === 'Approved') {
                        $building->update([
                            'status' => 'For Re-Approval',
                        ]);
                    }
                }

                if ($portal === 'owner' && $token['organization_id'] !== $unit->organization_id) {
                    throw new \Exception('The selected unit id is invalid.');
                }

                $oldBuildingId = $unit->building_id;
                $buildingChanged = ($oldBuildingId != $validated['building_id']);

                if ($buildingChanged) {
                    $subscriptionItem = PlanSubscriptionItem::where('organization_id', $organization_id)
                        ->where('service_catalog_id', 5)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $meta = $subscriptionItem->meta ?? ['quantity' => 0];


                    if (isset($meta[$oldBuildingId])) {
                        $meta[$oldBuildingId]['used'] = max(0, $meta[$oldBuildingId]['used'] - 1);
                        if ($meta[$oldBuildingId]['used'] <= 0) {
                            unset($meta[$oldBuildingId]);
                        }
                    }

                    $buildingCount = count(array_filter(array_keys($meta), 'is_int'));
                    if (!isset($meta[$validated['building_id']]) && $buildingCount >= $meta['quantity']) {
                        throw new \Exception('Building limit reached. Cannot move unit to new building.');
                    }

                    $newBuildingUnits = $meta[$validated['building_id']]['used'] ?? 0;
                    if ($newBuildingUnits >= $subscriptionItem->quantity) {
                        throw new \Exception('Target building has reached its unit limit (max ' . $subscriptionItem->quantity . ' units).');
                    }

                    $meta[$validated['building_id']] = [
                        'used' => $newBuildingUnits + 1
                    ];

                    $newHighest = max(array_column(array_filter($meta, 'is_array'), 'used')) ?? 0;
                    $subscriptionItem->update([
                        'used' => $newHighest,
                        'meta' => $meta
                    ]);
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
                    'status' => $status,
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
                    $organization_id,
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

                return redirect()->route($route)->with('success', 'Unit updated successfully.');
            });
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Unit Update Failed: " . $e->getMessage());

            $errorType = $user->role_id === 2 ? 'plan_upgrade_error' : 'error';
            return redirect()->back()->with($errorType, $e->getMessage())->withInput();
        }
    }


    // Owner Tree Function
    public function getUnitDetailsWithActiveContract(Request $request, $id)
    {
        try {
            $unit = BuildingUnit::with([
                'pictures' => function ($query) {
                    $query->select('unit_id', 'file_path');
                },
                'userUnits' => function ($query) {
                    $query->where('contract_status', 1)
                        ->select('id', 'unit_id', 'user_id')
                        ->with(['user' => function ($query) {
                            $query->select('id', 'name', 'picture');
                        }]);
                }
            ])->find($id);

            $buildingId = $unit->building_id ?? null;
            $organization_id = $this->validateOwnerAccess($request, 'You do not have access to view units of the selected building.', false, $buildingId);

            if ($organization_id instanceof RedirectResponse) {
                return $organization_id;
            }

            if (!$unit || $unit->organization_id !== $organization_id) {
                return response()->json(['error' => 'Invalid Unit Id.'], 404);
            }

            return response()->json(['Unit' => $unit]);

        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
    private function validateOwnerAccess(Request $request, $error = null, $keepInput = true, $buildingId = null)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        if (!$token || empty($token['organization_id']) || empty($token['role_name'])) {
            $redirect = redirect()->back()->with('error', 'You cannot perform this action. Please switch to an organization account to proceed.');
            return $keepInput ? $redirect->withInput() : $redirect;
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];
        $buildingId = $buildingId ?? $request->building_id;


        if ($role_name === 'Manager' && !ManagerBuilding::where('building_id', $buildingId)
                ->where('user_id', $user->id)
                ->exists()) {
            $redirect = redirect()->back()->with('error', $error);
            return $keepInput ? $redirect->withInput() : $redirect;
        }

        return $organization_id;
    }

    private function getBuildingsOwner($organization_id, $role_name, $user_id)
    {
        $query = Building::select('id', 'name')
            ->where('organization_id', $organization_id);

        if ($role_name === 'Manager') {
            $managerBuildingIds = ManagerBuilding::where('user_id', $user_id)->pluck('building_id')->toArray();
            $query->whereIn('id', $managerBuildingIds);
        }

        return $query->get();
    }


    // Get Building Available Units
    public function getAvailableBuildingUnits($building_id){

        $units = BuildingUnit::select('id', 'unit_name')
            ->where('sale_or_rent', '!=', 'Not Available')
            ->where('availability_status', 'Available')
            ->where('status', 'Approved')
            ->where('building_id', $building_id)
            ->get();

        return response()->json(['units' => $units]);
    }

    // Get Building Units by type
    public function getBuildingUnitsByType ($building_id, $unit_type){

        $units = BuildingUnit::select('id', 'unit_name')
            ->where('sale_or_rent', 'Not Available')
            ->where('unit_type', $unit_type)
            ->where('status', 'Approved')
            ->where('building_id', $building_id)
            ->get();

        return response()->json(['units' => $units]);
    }

}
