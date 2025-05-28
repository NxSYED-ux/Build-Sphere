<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\BuildingNotifications;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\ManagerBuilding;
use App\Models\PlanSubscriptionItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BuildingLevelController extends Controller
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
            $selectedStatus = $request->input('status');

            $levelsQuery = BuildingLevel::with('building')
                ->whereHas('building', function ($query) use ($allowedStatusesForBuilding) {
                    $query->whereIn('status', $allowedStatusesForBuilding);
                });

            if ($search) {
                $levelsQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            if ($selectedOrganization) {
                $levelsQuery->where('organization_id', $selectedOrganization);
            }

            if ($selectedBuildingId) {
                $levelsQuery->where('building_id', $selectedBuildingId);
            }

            if ($selectedStatus) {
                $levelsQuery->where('status', $selectedStatus);
            }

            $levels = $levelsQuery->paginate(12);

            $organizations = $adminService->organizations();
            $buildings = $adminService->buildings();
            $statuses = ['Approved', 'Rejected'];

            return view('Heights.Admin.Levels.index', compact('levels', 'organizations', 'buildings', 'statuses' ));
        } catch (\Throwable $e) {
            Log::error('Error in adminIndex Levels: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function ownerIndex(Request $request)
    {
        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();


            $search = $request->input('search');
            $buildingId = $request->input('building_id');
            $status = $request->input('status');

            $levelQuery = BuildingLevel::with(['building'])
                ->where('organization_id', $organization_id)
                ->whereIn('building_id', $buildingIds);

            if ($search) {
                $levelQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            if ($buildingId) {
                $levelQuery->where('building_id', $buildingId);
            }

            if ($status) {
                $levelQuery->where('status', $status);
            }

            $levels = $levelQuery->get();
            $buildings = $ownerService->buildings($buildingIds);
            $statuses = ['Approved', 'Rejected'];

            return view('Heights.Owner.Levels.index', compact('levels', 'buildings', 'statuses'));

        } catch (\Throwable $e) {
            Log::error('Owner Index Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Create
    public function adminCreate(): JsonResponse
    {
        $buildings = Building::select('id', 'name', 'organization_id')
            ->whereNotIn('status', ['Under Processing', 'Under Review', 'Rejected'])
            ->get();

        return response()->json($buildings);
    }

    public function ownerCreate()
    {
        $buildings = $this->getOwnerBuildings();

        if ($buildings instanceof JsonResponse) {
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

                if($portal === 'owner'){
                    $building = Building::where('id', $level->building_id)->first();

                    if($building->status === 'Approved') {
                        $building->update([
                            'status' => 'For Re-Approval',
                        ]);
                    }
                }

                $subscriptionItem = PlanSubscriptionItem::where('organization_id', $organization_id)
                    ->where('service_catalog_id', 4)
                    ->lockForUpdate()
                    ->first();

                if (!$subscriptionItem) {
                    throw new \Exception('The current plan doesn\'t include level management.');
                }

                $meta = $subscriptionItem->meta ?? ['quantity' => 0];

                if ($subscriptionItem->quantity <= 0 || $meta['quantity'] <= 0) {
                    throw new \Exception('The current plan doesn\'t include level management. Please upgrade the plan.');
                }

                $currentBuildingLevel = $meta[$level->building_id]['used'] ?? 0;
                if ($currentBuildingLevel >= $subscriptionItem->quantity) {
                    throw new \Exception('This building has reached its level limit (max ' . $subscriptionItem->quantity .' levels).');
                }

                $buildingCount = count(array_filter(array_keys($meta), 'is_int'));
                if (!isset($meta[$level->building_id]) && $buildingCount >= $meta['quantity']) {
                    throw new \Exception('Building limit reached. Upgrade the organization plan to add more buildings.');
                }

                $meta[$level->building_id] = [
                    'used' => ($meta[$level->building_id]['used'] ?? 0) + 1
                ];

                $newHighest = max(array_column(array_filter($meta, 'is_array'), 'used')) ?? 0;
                $subscriptionItem->update([
                    'used' => $newHighest,
                    'meta' => $meta
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
    public function show(BuildingLevel $level): JsonResponse
    {
        $level->load(['building']);
        return response()->json($level);
    }


    // Edit
    public function adminEdit(BuildingLevel $level): JsonResponse
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

    public function ownerEdit(BuildingLevel $level)
    {
        try {
            $level->load(['building']);
            $buildings = $this->getOwnerBuildings();

            if ($buildings instanceof JsonResponse) {
                return $buildings;
            }

            return response()->json([
                'level' => $level,
                'buildings' => $buildings
            ], 200);

        } catch (\Throwable $e) {
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

    private function update(Request $request, string $portal, $organization_id)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        $validated = $request->validate([
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
            return DB::transaction(function () use ($request, $portal, $organization_id, $user, $token, $validated) {
                $buildingLevel = BuildingLevel::where([
                    ['id', '=', $validated['level_id']],
                    ['updated_at', '=', $validated['updated_at']]
                ])->lockForUpdate()->first();

                if (!$buildingLevel) {
                    throw new \Exception('Please refresh and try again.');
                }

//                if ($buildingLevel->status === 'Approved' && $buildingLevel->building_id != $validated['building_id']) {
//                    throw new \Exception("You cannot change the building of a level that has already been approved.");
//                }

                if ($portal === 'owner' && $token['organization_id'] !== $buildingLevel->organization_id) {
                    throw new \Exception('The selected level id is invalid.');
                }

                $oldBuildingId = $buildingLevel->building_id;
                $buildingChanged = $oldBuildingId != $validated['building_id'];

                if ($buildingChanged) {
                    $subscriptionItem = PlanSubscriptionItem::where('organization_id', $organization_id)
                        ->where('service_catalog_id', 4)
                        ->lockForUpdate()
                        ->first();

                    if (!$subscriptionItem) {
                        throw new \Exception('The current plan doesn\'t include level management.');
                    }

                    $meta = $subscriptionItem->meta ?? ['quantity' => 0];


                    if (isset($meta[$oldBuildingId])) {
                        $meta[$oldBuildingId]['used'] = max(0, $meta[$oldBuildingId]['used'] - 1);
                        if ($meta[$oldBuildingId]['used'] <= 0) {
                            unset($meta[$oldBuildingId]);
                        }
                    }

                    $buildingCount = count(array_filter(array_keys($meta), 'is_int'));
                    if (!isset($meta[$validated['building_id']]) && $buildingCount >= $meta['quantity']) {
                        throw new \Exception('Building limit reached. Cannot move level to new building.');
                    }

                    $newBuildingLevels = $meta[$validated['building_id']]['used'] ?? 0;
                    if ($newBuildingLevels >= $subscriptionItem->quantity) {
                        throw new \Exception('Target building has reached its level limit (max ' . $subscriptionItem->quantity . ' levels).');
                    }

                    $meta[$validated['building_id']] = [
                        'used' => $newBuildingLevels + 1
                    ];

                    $newHighest = max(array_column(array_filter($meta, 'is_array'), 'used')) ?? 0;
                    $subscriptionItem->update([
                        'used' => $newHighest,
                        'meta' => $meta
                    ]);
                }

                $buildingLevel->update([
                    'level_name' => $validated['level_name'],
                    'description' => $validated['description'] ?? null,
                    'level_number' => $validated['level_number'],
                    'status' => $request->status ?? $buildingLevel->status,
                    'building_id' => $validated['building_id'],
                    'organization_id' => $organization_id,
                    'updated_at' => now(),
                ]);

                if($portal === 'owner'){
                    $building = Building::where('id', $buildingLevel->building_id)->first();

                    if($building->status === 'Approved') {
                        $building->update([
                            'status' => 'For Re-Approval',
                        ]);
                    }
                }

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
                        "Level Updated by Admin",
                        "The level '{$notificationData['level_name']}' has been updated by admin.",
                        'owner/levels',
                        $notificationData['user_id'],
                        "Level Updated",
                        "The level '{$notificationData['level_name']}' has been updated.",
                        'admin/levels',
                        true
                    ));
                } elseif ($portal === 'owner') {
                    dispatch(new BuildingNotifications(
                        $notificationData['organization_id'],
                        $notificationData['building_id'],
                        "Level Updated by {$notificationData['role_name']} ({$notificationData['user_name']})",
                        "The level '{$notificationData['level_name']}' has been updated by {$notificationData['role_name']}.",
                        'owner/levels',
                        $notificationData['user_id'],
                        "Level Updated",
                        "The level '{$notificationData['level_name']}' has been updated.",
                        'owner/levels'
                    ));
                }

                return redirect()->back()->with('success', 'Building Level updated successfully.');
            });
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Building Level Update Failed: " . $e->getMessage());

            $message = $e->getMessage();
            $planErrors = [
                'doesn\'t include level management',
                'Building limit reached',
                'level limit'
            ];

            $isPlanError = Str::contains($message, $planErrors);
            $errorType = ($user->role_id === 2 && $isPlanError) ? 'plan_upgrade_error' : 'error';

            return redirect()->back()->with($errorType, $message)->withInput();
        }

    }


    // Helper Functions
    private function getOwnerBuildings()
    {
        $user = request()->user() ?? abort(404, 'Unauthorized');
        $token = request()->attributes->get('token');
        $buildings = collect();

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return response()->json(['buildings' => $buildings]);
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
            return redirect()->back()->withInput()->with('error', 'You do not have access to add levels to the selected building.');
        }

        return $organization_id;
    }

}
