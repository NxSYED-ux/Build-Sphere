<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\BuildingNotifications;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\ManagerBuilding;
use App\Models\PlanSubscriptionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    private function store(Request $request, string $portal, $status, $organization_id)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        // Validate request
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

        if ($portal === 'owner' && $token['organization_id'] !== $organization_id) {
            return redirect()->back()->withInput()->with('error', 'You cannot perform this action.');
        }

        try {
            return DB::transaction(function () use ($request, $portal, $status, $organization_id, $user, $token, $validated) {

                $level = BuildingLevel::create([
                    'level_name' => $validated['level_name'],
                    'level_number' => $validated['level_number'],
                    'description' => $validated['description'] ?? null,
                    'building_id' => $validated['building_id'],
                    'organization_id' => $organization_id,
                    'status' => $status,
                ]);

                $subscriptionItem = PlanSubscriptionItem::where('organization_id', $organization_id)
                    ->where('service_catalog_id', 4)
                    ->lockForUpdate()
                    ->first();

                if (!$subscriptionItem) {
                    throw new \Exception('The current plan doesn\'t include level management.');
                }

                $meta = $subscriptionItem->meta ? json_decode($subscriptionItem->meta, true) : ['quantity' => 0];

                if ($subscriptionItem->quantity <= 0 || $meta['quantity'] <= 0) {
                    throw new \Exception('The current plan doesn\'t include level management. Please upgrade the plan.');
                }

                if ($subscriptionItem->used >= $subscriptionItem->quantity) {
                    throw new \Exception('Level limit reached. Upgrade the organization plan to add more levels.');
                }

                $buildingCount = count(array_filter(array_keys($meta), 'is_int'));
                if (!isset($meta[$level->building_id]) && $buildingCount >= $meta['quantity']) {
                    throw new \Exception('Building limit reached. Upgrade the organization plan to add more buildings.');
                }

                $meta[$level->building_id] = [
                    'used' => ($meta[$level->building_id]['used'] ?? 0) + 1
                ];

                $subscriptionItem->update([
                    'used' => $subscriptionItem->used + 1,
                    'meta' => json_encode($meta)
                ]);

                $notificationData = [
                    'organization_id' => $organization_id,
                    'building_id' => $validated['building_id'],
                    'level_name' => $validated['level_name'],
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'role_name' => $token['role_name'] ?? null,
                ];

                if ($portal === 'admin') {
                    dispatch(new BuildingNotifications(
                        $notificationData['organization_id'],
                        $notificationData['building_id'],
                        "New Level Created by Admin",
                        "The level '{$notificationData['level_name']}' has been successfully created by admin.",
                        'owner/levels',
                        $notificationData['user_id'],
                        "New Level Created",
                        "The level '{$notificationData['level_name']}' has been created.",
                        'admin/levels',
                        true
                    ));
                } elseif ($portal === 'owner') {
                    dispatch(new BuildingNotifications(
                        $notificationData['organization_id'],
                        $notificationData['building_id'],
                        "New Level Created by {$notificationData['role_name']} ({$notificationData['user_name']})",
                        "The level '{$notificationData['level_name']}' has been created by {$notificationData['role_name']}.",
                        'owner/levels',
                        $notificationData['user_id'],
                        "New Level Created",
                        "The level '{$notificationData['level_name']}' has been created.",
                        'owner/levels'
                    ));
                }

                return redirect()->back()->with('success', 'Building Level created successfully.');
            });
        } catch (\Exception $e) {
            DB::rollBack();

            $errorType = $user->role_id === 2 ? 'plan_upgrade_error' : 'error';
            $message = $e->getMessage();

            Log::error("Building Level Creation Failed: {$message}");

            return redirect()->back()->with($errorType, $message)->withInput();
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
