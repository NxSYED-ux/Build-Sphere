<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\BuildingNotifications;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\ManagerBuilding;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BuildingLevelController extends Controller
{
    // Index
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


    // Create
    public function adminCreate()
    {
        $buildings = Building::select('id', 'name', 'organization_id')
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


    // Store
    public function adminStore(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);
        return $this->store($request, 'admin','Approved', $request->organization_id);
    }

    public function ownerStore(Request $request)
    {
        $organization_id = $this->ownerBuildingAccess($request);
        if ($organization_id instanceof RedirectResponse) {
            return $organization_id;
        }

        return $this->store($request, 'owner','Rejected', $organization_id);
    }

    private function store(Request $request, String $portal, $status, $organization_id)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        $request->validate([
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

            if($portal === 'owner' && $token['organization_id'] !== $organization_id){
                return redirect()->back()->withInput()->with('error', 'You can not perform this action.');
            }

            BuildingLevel::create([
                'level_name' => $request->level_name,
                'level_number' => $request->level_number,
                'description' => $request->description,
                'building_id' => $request->building_id,
                'organization_id' => $organization_id,
                'status' => $status,
            ]);

            if($portal === 'admin'){
                dispatch( new BuildingNotifications(
                   $organization_id,
                   $request->building_id,
                   "New Level Created by Admin",
                   "The level '{$request->level_name}' has been successfully created by admin and is now available for use.",
                   'owner.levels.index',

                   $user->id,
                    "New Level Created",
                    "The level '{$request->level_name}' has been successfully created and is now available for use.",
                    'levels.index',

                   true,
                ));
            }elseif($portal === 'owner'){
                dispatch( new BuildingNotifications(
                   $organization_id,
                   $request->building_id,
                   "New Level Created by {$token['role_name']} ({$user->name})",
                   "The level '{$request->level_name}' has been successfully created by {$token['role_name']}.",
                   'owner.levels.index',

                   $user->id,
                    "New Level Created",
                    "The level '{$request->level_name}' has been successfully created successfully.",
                    'owner.levels.index',
                ));
            }

            return redirect()->back()->with('success', 'Building Level created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in create Building Level: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Show
    public function show(BuildingLevel $level)
    {
        $level->load(['building']);
        return response()->json($level);
    }


    // Edit
    public function adminEdit(BuildingLevel $level)
    {
        try {
            $level->load(['building']);

            $buildings = Building::select('id', 'name', 'organization_id')
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


    // Update
    public function adminUpdate(Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:Approved,Rejected',
            'organization_id' => 'required|exists:organizations,id',
        ]);
        return $this->update($request, 'admin', $request->organization_id);
    }

    public function ownerUpdate(Request $request)
    {
        $organization_id = $this->ownerBuildingAccess($request);
        if ($organization_id instanceof RedirectResponse) {
            return $organization_id;
        }

        return $this->update($request, 'owner', $organization_id);
    }

    private function update(Request $request, String $portal, $organization_id)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        $request->validate([
            'level_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('buildinglevels')->where(function ($query) use ($request) {
                    return $query->where('building_id', $request->building_id);
                })->ignore($request->level_id),
            ],
            'level_id' => 'required|exists:buildinglevels,id',
            'description' => 'nullable|string',
            'level_number' => 'required|integer',
            'building_id' => 'required|exists:buildings,id',
            'updated_at' => 'required'
        ], [
            'level_name.unique' => 'This level name is already in use for the selected building.',
        ]);

        try {

            if($portal === 'owner' && $token['organization_id'] !== $organization_id){
                return redirect()->back()->with('error', 'You can not perform this action.');
            }

            $buildingLevel = BuildingLevel::where([
                ['id', '=', $request->level_id],
                ['updated_at', '=', $request->updated_at]
            ])->sharedLock()->first();

            if (!$buildingLevel) {
                return redirect()->back()->with('error', 'Please refresh and try again.');
            }

            $buildingLevel->update([
                'level_name' => $request->level_name,
                'description' => $request->description,
                'level_number' => $request->level_number,
                'status' => $request->status ?? $buildingLevel->status,
                'building_id' => $request->building_id,
                'organization_id' => $organization_id,
                'updated_at' => $request->updated_at,
            ]);

            if($portal === 'admin'){
                dispatch( new BuildingNotifications(
                    $organization_id,
                    $request->building_id,
                    "Level Updated by Admin",
                    "The level '{$request->level_name}' has been successfully updated by admin.",
                    'owner.levels.index',

                    $user->id,
                    "New Level Created",
                    "The level '{$request->level_name}' has been successfully updated with the applied changes.",
                    'levels.index',

                    true,
                ));
            }elseif($portal === 'owner'){
                dispatch( new BuildingNotifications(
                    $organization_id,
                    $request->building_id,
                    "Level Updated by {$token['role_name']} ({$user->name})",
                    "The level '{$request->level_name}' has been successfully updated by {$token['role_name']}.",
                    'owner.levels.index',

                    $user->id,
                    "Level Updated",
                    "The level '{$request->level_name}' has been successfully updated with the applied changes.",
                    'owner.levels.index',
                ));
            }

            return redirect()->back()->with('success', 'Building Level updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in update Building Level: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
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

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        if ($role_name === 'Manager' && !ManagerBuilding::where('building_id', $request->building_id)
                ->where('user_id', $user->id)
                ->exists()) {
            return redirect()->back()->withInput()->with('error', 'You do not have access to add units of the selected building.');
        }

        return $organization_id;
    }

}
