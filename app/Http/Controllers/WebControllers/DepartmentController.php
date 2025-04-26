<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\BuildingNotifications;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\Department;
use App\Models\ManagerBuilding;
use App\Notifications\DatabaseOnlyNotification;
use App\Notifications\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    // Index
    public function index(Request $request)
    {
        try {
            $token = $request->attributes->get('token');

            $departments = collect();
            if (!empty($token['organization_id'])) {
                $departments = Department::where('organization_id', $token['organization_id'])->get();
            }

            return view('Heights.Owner.Departments.index', compact('departments'));

        } catch (\Exception $e) {
            Log::error('Department Index Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Store
    public function store(Request $request)
    {
        $token = $request->attributes->get('token');
        if (empty($token['organization_id'])) {
            return redirect()->back()->with('error', "You can't perform this action.");
        }
        $organization_id = $token['organization_id'];

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments')->where(function ($query) use ($organization_id) {
                    return $query->where('organization_id', $organization_id);
                }),
            ],
            'description' => 'nullable|string',
        ], [
            'name.unique' => 'This department name is already in use.',
        ]);

        try {
            $department = Department::create([
                'name' => $request->name,
                'description' => $request->description,
                'organization_id' => $organization_id,
            ]);

            dispatch( new DatabaseOnlyNotification(
                null,
                "New Department Created",
                "The department '{$request->level_name}' has been successfully created.",
                ['web' => "owner/departments/{$department->id}"]
            ));

            return redirect()->back()->with('success', 'Departemnt created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in Department store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Show
    public function show(Request $request, Department $department)
    {
        $token = $request->attributes->get('token');
        if (empty($token['organization_id'])) {
            return redirect()->back()->with('error', "You can't perform this action.");
        }
        $organization_id = $token['organization_id'];

        if($department->organization_id ){

        }

        $staffMembers = $department->staffMembers()
            ->with('Users')
            ->paginate(10);

        return view('Owner.Departments.show', compact('department', 'staffMembers'));
    }


    // Edit
    public function edit(Request $request,Department $department)
    {
        try {
            return response()->json([
                'department' => $department,
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
            DB::beginTransaction();

            $buildingLevel = BuildingLevel::where([
                ['id', '=', $request->level_id],
                ['updated_at', '=', $request->updated_at]
            ])->sharedLock()->first();

            if (!$buildingLevel) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Please refresh and try again.');
            }

            if($portal === 'owner' && $token['organization_id'] !== $buildingLevel->organization_id){
                DB::rollBack();
                return redirect()->back()->with('error', 'The selected level id is invalid.');
            }

            $buildingLevel->update([
                'level_name' => $request->level_name,
                'description' => $request->description,
                'level_number' => $request->level_number,
                'status' => $request->status ?? $buildingLevel->status,
                'building_id' => $request->building_id,
                'organization_id' => $organization_id,
                'updated_at' => now(),
            ]);

            DB::commit();

            if($portal === 'admin'){
                dispatch( new BuildingNotifications(
                    $organization_id,
                    $request->building_id,
                    "Level Updated by Admin",
                    "The level '{$request->level_name}' has been successfully updated by admin.",
                    'owner/levels',

                    $user->id,
                    "Level Updated",
                    "The level '{$request->level_name}' has been successfully updated with the applied changes.",
                    'admin/levels',

                    true,
                ));
            }elseif($portal === 'owner'){
                dispatch( new BuildingNotifications(
                    $organization_id,
                    $request->building_id,
                    "Level Updated by {$token['role_name']} ({$user->name})",
                    "The level '{$request->level_name}' has been successfully updated by {$token['role_name']}.",
                    'owner/levels',

                    $user->id,
                    "Level Updated",
                    "The level '{$request->level_name}' has been successfully updated with the applied changes.",
                    'owner/levels',
                ));
            }

            return redirect()->back()->with('success', 'Building Level updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
