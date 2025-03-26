<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\ManagerBuilding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BuildingLevelController extends Controller
{
    public function adminIndex(Request $request)
    {
        try {
            $buildingId = $request->input('building_id');

            $levels = BuildingLevel::with(['building'])
                ->whereHas('building', function ($query) {
                    $query->whereNotIn('status', ['Under Processing', 'Rejected']);
                })
                ->when($buildingId, function ($query) use ($buildingId) {
                    $query->where('building_id', $buildingId);
                })
                ->get();

            return view('Heights.Admin.Levels.index', compact('levels'));
        } catch (\Exception $e) {
            Log::error('Error in admin Index Levels' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function ownerIndex(Request $request)
    {
        try {
            $user = $request->user() ?? abort(404, 'Unauthorized');
            $token = $request->attributes->get('token');
            $levels = collect();

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.Levels.index', compact('levels'));
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];
            $buildingId = $request->input('building_id');

            $query = BuildingLevel::with(['building'])
                ->whereHas('building', function ($q) use ($organization_id) {
                    $q->where('organization_id', $organization_id);
                })
                ->when($buildingId, function ($query) use ($buildingId) {
                    $query->where('building_id', $buildingId);
                });

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
                $query->whereIn('building_id', $managerBuildingIds);
            }

            $levels = $query->get();
            return view('Heights.Owner.Levels.index', compact('levels'));

        } catch (\Exception $e) {
            Log::error('Owner Index Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function adminCreate()
    {
        $buildings = Building::select('id', 'name')
            ->whereNotIn('status', ['Under Processing', 'Under Review', 'Rejected'])
            ->get();

        return response()->json($buildings);
    }

    public function ownerCreate(Request $request)
    {
        $buildings = $this->getOwnerBuildings($request);

        if(!$buildings instanceof Building){
            return $buildings;
        }

        return response()->json($buildings);
    }


    public function adminStore(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);
        return $this->store($request, 'admin','Approved');
    }

    public function ownerStore(Request $request)
    {
        if ($response = $this->ownerBuildingAccess($request)) {
            return $response;
        }

        return $this->store($request, 'owner','Rejected');
    }

    private function store(Request $request, String $portal, $status)
    {
        $validated = $request->validate([
            'level_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('buildinglevels')->where(function ($query) use ($request) {
                    return $query->where('building_id', $request->building_id);
                }),
            ],
            'description' => 'nullable|string',
            'level_number' => 'required|integer',
            'building_id' => 'required|exists:buildings,id',
        ], [
            'level_name.unique' => 'This level name is already in use for the selected building.',
        ]);

        try {
            BuildingLevel::create($validated);

            return redirect()->back()->with('success', 'Building Level created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in create Building Level: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function show(BuildingLevel $level)
    {
        $level->load(['building']);
        return response()->json($level);
    }

    public function adminEdit(BuildingLevel $level)
    {
        try {
            $level->load(['building']);

            $buildings = Building::select('id', 'name')
                ->whereNotIn('status', ['Under Processing', 'Under Review', 'Rejected'])
                ->get();

            return response()->json([
                'level' => $level,
                'buildings' => $buildings
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching building level data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching building level data.'], 500);
        }
    }

    public function ownerEdit(Request $request,BuildingLevel $level)
    {
        try {
            $level->load(['building']);
            $buildings = $this->getOwnerBuildings($request);

            if(!$buildings instanceof Building){
                return $buildings;
            }

            return response()->json([
                'level' => $level,
                'buildings' => $buildings
            ]);

        } catch (\Exception $e) {
            Log::error('Error in ownerEdit: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching building level data.'], 500);
        }
    }


    public function adminUpdate(Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:Approved,Rejected',
        ]);
        return $this->update($request, 'admin');
    }

    public function ownerUpdate(Request $request)
    {
        if ($response = $this->ownerBuildingAccess($request)) {
            return $response;
        }

        return $this->update($request, 'owner');
    }

    private function update(Request $request, String $portal)
    {
        $request->validate([
            'level_id' => 'required|integer|exists:buildinglevels,id',
            'building_id' => 'required|exists:buildings,id',
            'level_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('buildinglevels')->where(function ($query) use ($request) {
                    return $query->where('building_id', $request->building_id);
                })->ignore($request->level_id),
            ],
            'description' => 'nullable|string',
            'level_number' => 'required|integer',
            'updated_at' => 'required'
        ]);

        try {
            $buildingLevel = BuildingLevel::where([
                ['id', '=', $request->level_id],
                ['updated_at', '=', $request->updated_at]
            ])->sharedLock()->first();

            if (!$buildingLevel) {
                return redirect()->back()->with('error', 'The building level data has been modified by another user. Please refresh and try again.');
            }

            $buildingLevel->update([
                'level_name' => $request->level_name,
                'description' => $request->description,
                'level_number' => $request->level_number,
                'status' => $request->status ?? $buildingLevel->status,
                'building_id' => $request->building_id,
            ]);

            return redirect()->back()->with('success', 'Building Level updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating Building Level: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Helper Functions
    private function getOwnerBuildings(Request $request)
    {
        $user = $request->user() ?? abort(404, 'Unauthorized');
        $token = $request->attributes->get('token');
        $buildings = collect();

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return response()->json($buildings);
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        $query = Building::select('id', 'name')->where('organization_id', $organization_id);

        if ($role_name === 'Manager') {
            $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
            $query->whereIn('id', $managerBuildingIds);
        }

        return $query->get();
    }

    private function ownerBuildingAccess(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        if (!$token || !isset($token['organization_id']) || !isset($token['role_name'])) {
            return redirect()->back()->withInput()->with('error', 'You cannot perform this action because they are not linked to any organization. Please switch to an organization account to proceed.');
        }

        $role_name = $token['role_name'];

        if ($role_name === 'Manager' && !ManagerBuilding::where('building_id', $request->building_id)
                ->where('user_id', $user->id)
                ->exists()) {
            return redirect()->back()->withInput()->with('error', 'You do not have access to add units of the selected building.');
        }

        return null;
    }

}
