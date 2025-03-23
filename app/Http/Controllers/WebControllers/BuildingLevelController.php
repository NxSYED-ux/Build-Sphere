<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\ManagerBuilding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BuildingLevelController extends Controller
{
    public function adminIndex(Request $request)
    {
        try {
            $buildingId = $request->input('building_id');

            $levels = BuildingLevel::with(['building'])
                ->where('status', 'Approved')
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
        $buildings = collect();
        $token = $request->attributes->get('token');

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return response()->json($buildings);
        }

        $organization_id = $token['organization_id'];

        $buildings = Building::select('id', 'name')
            ->where('organization_id',$organization_id)
            ->get();

        return response()->json($buildings);
    }

    public function adminStore(Request $request){
        return $this->store($request, 'admin');
    }

    public function ownerStore(Request $request){
        return $this->store($request, 'owner');
    }

    private function store(Request $request, String $portal)
    {
        try {
            if (!in_array($portal, ['admin', 'owner'])) {
                abort(404, 'Page not found');
            }

            $validated = $request->validate([
                'level_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'level_number' => 'required|integer',
                'status' => 'required|string|in:Approved,Rejected',
                'building_id' => 'required|exists:buildings,id',
            ]);

            BuildingLevel::create($validated);

            $route = $portal === 'admin' ? 'levels.index' : 'owner.levels.index';
            return redirect()->route($route)->with('success', 'Building Level created successfully.');

        } catch (\Exception $e) {
            Log::error('Error in create Building Level: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function show(BuildingLevel $buildingLevel)
    {
        return view('Heights.Admin.Levels.show', compact('buildingLevel'));
    }

    public function adminEdit(BuildingLevel $level)
    {
        $level->load(['building']);

        if ($level) {
            $buildings = Building::select('id', 'name')
                ->whereNotIn('status', ['Under Processing', 'Under Review', 'Rejected'])
                ->get();

            return response()->json([
                'level' => $level,
                'buildings' => $buildings
            ]);

        }
        return response()->json(['message' => 'Not found'], 404);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'level_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level_number' => 'required|integer',
            'status' => 'required|string|in:Approved,Rejected',
            'building_id' => 'required|exists:buildings,id',
        ]);

        $buildingLevel = BuildingLevel::findorfail($id);

        $buildingLevel->update([
            'level_name' => $request->level_name,
            'description' => $request->description,
            'level_number' => $request->level_number,
            'status' => $request->status,
            'building_id' => $request->building_id,
        ]);

        if(!$buildingLevel){
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the building.');
        }

        return redirect()->route('levels.index')->with('success', 'Building Level updated successfully.');
    }
}
