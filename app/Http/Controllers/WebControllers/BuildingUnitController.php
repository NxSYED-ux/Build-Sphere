<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\BuildingUnit;
use App\Models\DropdownType;
use App\Models\ManagerBuilding;
use App\Models\Organization;
use App\Models\UnitPicture;
use App\Models\UserBuildingUnit;
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
            $user = $request->user() ?? abort(403, 'Unauthorized');
            $token = $request->attributes->get('token');

            $buildings = collect();
            $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
            $unitTypes = $unitType ? $unitType->values : collect();

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.Units.create', compact('buildings', 'unitTypes'));
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $query = Building::select('id', 'name')
                ->where('organization_id',$organization_id);

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
                $query->whereIn('id', $managerBuildingIds);
            }

            $buildings = $query->get();

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
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        if (!$token || !isset($token['organization_id']) || !isset($token['role_name'])) {
            return redirect()->back()->withInput()->with('error', 'You cannot perform this action because they are not linked to any organization. Please switch to an organization account to proceed.');
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        if ($role_name === 'Manager' && !ManagerBuilding::where('building_id', $request->building_id)
                ->where('user_id', $user->id)
                ->exists()) {
            return redirect()->back()->withInput()->with('error', 'You do not have access to add units of the selected building.');
        }

        return $this->store($request, 'owner',$organization_id,'Rejected');
    }

    private function store(Request $request, String $portal, $organization_id, $status)
    {
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
            'availability_status' => 'required|string',
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
                'availability_status' => $request->availability_status,
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

            $route = $portal === 'admin' ? 'units.index' : 'owner.units.index';

            return redirect()->route($route)->with('success', 'Unit created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in unit store: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }



    public function show(BuildingUnit $buildingUnit)
    {

    }

    public function adminEdit(BuildingUnit $unit)
    {
        try {
            $organizations = Organization::where('status', 'Enable')->get();
            $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
            $unitTypes = $unitType ? $unitType->values : collect();

            return view('Heights.Admin.Units.edit', compact( 'unit', 'organizations', 'unitTypes'));

        } catch (\Exception $e) {
            Log::error('Error in adminCreate: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function ownerEdit(Request $request, BuildingUnit $unit)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        $buildings = collect();
        $unitType = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first();
        $unitTypes = $unitType ? $unitType->values : collect();

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return view('Heights.Owner.Units.create', compact('buildings', 'unitTypes'));
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        if ($organization_id !== $unit->organization_id) {
            abort(404, 'Page not found');
        }

        $query = Building::select('id', 'name')
            ->where('organization_id',$organization_id);

        if ($role_name === 'Manager') {
            $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
            $query->whereIn('id', $managerBuildingIds);
        }

        $buildings = $query->get();

        return view('Heights.Owner.Units.edit', compact( 'unit', 'buildings', 'unitTypes'));
    }


    public function adminUpdate(Request $request, BuildingUnit $unit)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);
        return $this->update($request, $unit, 'admin',$request->organization_id,'Approved');
    }

    public function ownerUpdate(Request $request, BuildingUnit $unit)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        if (!$token || !isset($token['organization_id']) || !isset($token['role_name'])) {
            return redirect()->back()->withInput()->with('error', 'You cannot perform this action because they are not linked to any organization. Please switch to an organization account to proceed.');
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        if ($organization_id !== $unit->organization_id) {
            abort(404, 'Page not found');
        }

        if ($role_name === 'Manager' && !ManagerBuilding::where('building_id', $request->building_id)
                ->where('user_id', $user->id)
                ->exists()) {
            return redirect()->back()->withInput()->with('error', 'You do not have access to add units of the selected building.');
        }

        return $this->update($request, $unit, 'owner', $organization_id,'Rejected');
    }

    private function update(Request $request, BuildingUnit $unit, String $portal, $organization_id, $status)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');

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
            'availability_status' => 'required|string',
            'level_id' => 'required|integer',
            'building_id' => 'required|integer',
            'unit_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],[
            'unit_name.unique' => 'This unit name is already in use for the selected building.',
        ]);

        DB::beginTransaction();

        try {
            $userHasActiveContract = UserBuildingUnit::where('unit_id', $unit->id)
                ->where('user_id', $user->id)
                ->where('contract_status', 1)
                ->exists();

            if (!$userHasActiveContract && $unit->availability_status !== $request->availability_status) {
                Log::error("Availability status error: ");
                return redirect()->back()->with('error', 'You cannot change the availability status of this unit because this unit is currently in a contract.');
            }

            $unit->update([
                'unit_name' => $request->unit_name,
                'unit_type' => $request->unit_type,
                'price' => $request->price,
                'description' => $request->description,
                'sale_or_rent' => $request->sale_or_rent,
                'area' => $request->area,
                'availability_status' => $request->availability_status,
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

            $route = $portal === 'admin' ? 'units.index' : 'owner.units.index';

            return redirect()->route($route)->with('success', 'Unit created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating unit: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the building unit.');
        }
    }


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

    public function getUnitData(string $id)
    {
        try {
            $id = (int) $id;

            if (!is_numeric($id) || $id <= 0) {
                return response()->json(['error' => 'Invalid Unit ID'], 400);
            }

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
            ])->select('id', 'unit_name', 'unit_type', 'price', 'sale_or_rent')
                ->find($id);

            if (!$unit) {
                return response()->json(['error' => 'Unit not found'], 404);
            }

            return response()->json(['Unit' => $unit], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching Unit Data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching unit data.'], 500);
        }
    }

}
