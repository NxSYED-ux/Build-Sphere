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

    private function store(Request $request, String $portal, $organization_id, $status)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        $request->validate([
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
            'level_id' => 'required|integer',
            'building_id' => 'required|integer',
            'unit_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],[
            'unit_name.unique' => 'This level name is already in use for the selected building.',
        ]);

        DB::beginTransaction();

        try {

            $unit = BuildingUnit::create([
                'unit_name' => $request->unit_name,
                'unit_type' => $request->unit_type,
                'price' => $request->price,
                'description' => $request->description,
                'sale_or_rent' => $request->sale_or_rent,
                'area' => $request->area,
                'level_id' => $request->level_id,
                'building_id' => $request->building_id,
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

            DB::commit();

            if($portal === 'admin'){

                $route = 'units.index';

                dispatch( new BuildingNotifications(
                    $organization_id,
                    $request->building_id,
                    "New Unit Created by Admin",
                    "The Unit '{$request->unit_name}' has been successfully created by admin and is now available.",
                    "owner/units/{$unit->id}/show",

                    $user->id,
                    "New Unit Created",
                    "The Unit '{$request->unit_name}' has been successfully created and is now available.",
                    "admin/units/{$unit->id}/show",

                    true,
                ));

            }
            elseif ($portal === 'owner'){

                $route = 'owner.units.index';

                dispatch( new BuildingNotifications(
                    $organization_id,
                    $request->building_id,
                    "New Unit Created by {$token['role_name']} ({$user->name})",
                    "The Unit '{$request->unit_name}' has been successfully created by {$token['role_name']}.",
                    "owner/units/{$unit->id}/show",

                    $user->id,
                    "New Unit Created",
                    "The Unit '{$request->unit_name}' has been successfully created.",
                    "owner/units/{$unit->id}/show",
                ));
            }
            else{
                abort(404, 'Page Not Found.');
            }

            return redirect()->route($route)->with('success', 'Unit created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in unit store: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
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

            $organization_id = $this->validateOwnerAccess($request,'You do not have access to view units of the selected building.',false, $unit->building_id);
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

    private function update(Request $request, String $portal, $organization_id, $status)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        $request->validate([
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
            'level_id' => 'required|integer',
            'building_id' => 'required|integer',
            'updated_at' => 'required',
            'unit_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],[
            'unit_name.unique' => 'This unit name is already in use for the selected building.',
        ]);

        DB::beginTransaction();

        try {
            $unit = BuildingUnit::where([
                ['id', '=', $request->unit_id],
                ['updated_at', '=', $request->updated_at]
            ])->sharedLock()->first();

            if (!$unit) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Please refresh the page and try again.');
            }

            if($portal === 'owner' && $token['organization_id'] !== $unit->organization_id){
                DB::rollBack();
                return redirect()->back()->with('error', 'The selected unit id is invalid.');
            }

            $unit->update([
                'unit_name' => $request->unit_name,
                'unit_type' => $request->unit_type,
                'price' => $request->price,
                'description' => $request->description,
                'sale_or_rent' => $request->sale_or_rent,
                'area' => $request->area,
                'level_id' => $request->level_id,
                'building_id' => $request->building_id,
                'organization_id' => $organization_id,
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

            DB::commit();

            if($portal === 'admin'){

                $route = 'units.index';

                dispatch( new BuildingNotifications(
                    $organization_id,
                    $request->building_id,
                    "Unit Updated by Admin",
                    "The Unit '{$request->unit_name}' has been successfully updated by admin.",
                    "owner/units/{$unit->id}/show",

                    $user->id,
                    "Unit Updated",
                    "The Unit '{$request->unit_name}' has been successfully updated with the applied changes.",
                    "admin/units/{$unit->id}/show",

                    true,
                ));

            }
            elseif ($portal === 'owner'){

                $route = 'owner.units.index';

                dispatch( new BuildingNotifications(
                    $organization_id,
                    $request->building_id,
                    "Unit Updated by {$token['role_name']} ({$user->name})",
                    "The Unit '{$request->unit_name}' has been successfully updated by {$token['role_name']}.",
                    "owner/units/{$unit->id}/show",

                    $user->id,
                    "Unit Updated",
                    "The Unit '{$request->unit_name}' has been successfully updated with the applied changes.",
                    "owner/units/{$unit->id}/show",
                ));
            }
            else{
                abort(404, 'Page Not Found.');
            }

            return redirect()->route($route)->with('success', 'Unit updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating unit: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the building unit.');
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
                        ->select('id', 'unit_id', 'user_id', 'rent_start_date', 'rent_end_date', 'purchase_date')
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


    // Get Building Units
    public function getAvailableBuildingUnits($building_id){

        $units = BuildingUnit::select('id', 'unit_name')
            ->where('sale_or_rent', '!=', 'Not Available')
            ->where('availability_status', 'Available')
            ->where('status', 'Approved')
            ->where('building_id', $building_id)
            ->get();

        return response()->json(['units' => $units]);
    }

}
