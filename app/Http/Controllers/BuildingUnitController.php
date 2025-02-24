<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\BuildingUnit;
use App\Models\BuildingLevel;
use App\Models\Organization;
use App\Models\UnitPicture;
use App\Models\DropdownType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;

class BuildingUnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $levelId = $request->input('level_id');

        $unitsQuery = BuildingUnit::with(['level.building', 'organization', 'pictures']);

        if (!empty($search)) {
            $unitsQuery->where(function ($query) use ($search) {
                $query->where('unit_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('level', function ($subQuery) use ($search) {
                        $subQuery->where('level_name', 'LIKE', "%{$search}%")
                            ->orWhereHas('building', function ($subSubQuery) use ($search) {
                                $subSubQuery->where('name', 'LIKE', "%{$search}%");
                            });
                    })
                    ->orWhereHas('organization', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }
        if ($levelId) {
            $unitsQuery->where('level_id', $levelId);
        }
        $units = $unitsQuery->paginate(10);

        $units->appends(['search' => $search]);

        return view('Heights.Admin.Units.index', compact('units', 'search'));
    }

    public function create()
    {
        $buildings = Building::all();
        $levels = BuildingLevel::all();
        $organizations = Organization::all();
        $unitTypes = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first()->values;
        return view('Heights.Admin.Units.create', compact('buildings', 'levels', 'organizations', 'unitTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_name' => 'required|string|max:255',
            'unit_type' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'sale_or_rent' => 'required|string',
            'status' => 'nullable|string',
            'area' => 'nullable|numeric',
            'availability_status' => 'required|string',
            'level_id' => 'required|integer',
            'organization_id' => 'required|integer',
            'unit_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {

            $unit = BuildingUnit::create($validated);

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

            return redirect()->route('units.index')->with('success', 'Unit created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the unit.');
        }
    }

    public function show(BuildingUnit $buildingUnit)
    {

    }

    public function edit(BuildingUnit $unit)
    {
        $unit->load(['level', 'organization']);
        $buildings = Building::all();
        $levels = BuildingLevel::all();
        $organizations = Organization::all();
        $unitTypes = DropdownType::with(['values'])->where('type_name', 'Unit-type')->first()->values;
        return view('Heights.Admin.Units.edit', compact('buildings', 'unit', 'levels', 'organizations', 'unitTypes'));
    }

    public function update(Request $request, BuildingUnit $unit)
    {
        $validated = $request->validate([
            'unit_name' => 'required|string|max:255',
            'unit_type' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'sale_or_rent' => 'required|string',
            'status' => 'nullable|string',
            'area' => 'nullable|numeric',
            'availability_status' => 'required|string',
            'level_id' => 'required|integer',
            'organization_id' => 'required|integer',
            'unit_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {

            $unit->update($validated);

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

            return redirect()->route('units.index')->with('success', 'Building unit updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error creating unit: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the building unit.');
        }
    }

    public function destroyImage(string $id)
    {
        $image = UnitPicture::findOrFail($id);

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

    public function getBuildings($id)
    {
        $buildings = Building::where('organization_id', $id)->pluck('name', 'id'); // Fetch buildings for the organization
        return response()->json(['buildings' => $buildings]);
    }

    public function getLevels($buildingId)
    {
        $levels = BuildingLevel::where('building_id', $buildingId)->pluck('level_name', 'id');
        return response()->json(['levels' => $levels]);
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
