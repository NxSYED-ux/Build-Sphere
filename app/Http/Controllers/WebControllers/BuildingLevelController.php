<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\BuildingNotifications;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\PlanSubscriptionItem;
use App\Services\AdminFiltersService;
use App\Services\OwnerFiltersService;
use Illuminate\Http\JsonResponse;
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
                    $q->where('level_name', 'like', '%' . $search . '%')
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
                    $q->where('level_name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            if ($buildingId) {
                $levelQuery->where('building_id', $buildingId);
            }

            if ($status) {
                $levelQuery->where('status', $status);
            }

            $levels = $levelQuery->paginate(12);
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
        $adminService = new AdminFiltersService();
        $buildings = $adminService->buildings();

        return response()->json($buildings);
    }

    public function ownerCreate(): JsonResponse
    {
        $ownerService = new OwnerFiltersService();
        $buildingIds = $ownerService->getAccessibleBuildingIds();
        $buildings = $ownerService->buildings($buildingIds);

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
        $ownerService = new OwnerFiltersService();
        $result = $ownerService->checkBuildingAccess($request->building_id);

        if(!$result['access']){
            return redirect()->back()->with('error', $result['message']);
        }

        return $this->store($request, 'owner','Rejected', $result['organization_id']);
    }

    private function store(Request $request, string $portal, $status, $organization_id)
    {
        $user = $request->user();
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
        } catch (\Throwable $e) {
            DB::rollBack();

            $errorType = $user->role_id === 2 ? 'plan_upgrade_error' : 'error';
            $message = $e->getMessage();

            Log::error("Building Level Creation Failed: {$message}");

            return redirect()->back()->with($errorType, $message)->withInput();
        }
    }


    // Edit
    public function edit(BuildingLevel $level): JsonResponse
    {
        try {
            $level->load(['building']);

            return response()->json([
                'level' => $level,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error fetching building level data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching level data.'], 500);
        }
    }


    // Update
    public function adminUpdate(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);
        return $this->update($request, 'admin', $request->organization_id);
    }

    public function ownerUpdate(Request $request)
    {
        $token = request()->attributes->get('token');
        $organization_id = $token['organization_id'];

        return $this->update($request, 'owner', $organization_id);
    }

    private function update(Request $request, string $portal, $organization_id)
    {
        $validated = $request->validate([
            'level_id' => 'required|exists:buildinglevels,id',
            'level_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level_number' => 'required|integer',
            'updated_at' => 'required',
        ]);

        try {
            $user = $request->user();
            $token = $request->attributes->get('token');

            DB::beginTransaction();

            $buildingLevel = BuildingLevel::where([
                ['id', '=', $validated['level_id']],
                ['updated_at', '=', $validated['updated_at']],
            ])->lockForUpdate()->first();

            if (!$buildingLevel) {
                return redirect()->back()->withInput()->with('error', 'Please refresh and try again.');
            }

            if ($portal === 'owner' && $token['organization_id'] !== $buildingLevel->organization_id) {
                return redirect()->back()->withInput()->with('error', 'The selected level id is invalid.');
            }

            if($portal === 'owner'){
                $ownerService = new OwnerFiltersService();
                $result = $ownerService->checkBuildingAccess($buildingLevel->building_id);

                if(!$result['access']){
                    return redirect()->back()->with('error', $result['message']);
                }
            }

            $exists = BuildingLevel::where('building_id', $buildingLevel->building_id)
                ->where('level_name', $validated['level_name'])
                ->where('id', '!=', $validated['level_id'])
                ->exists();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'This level name is already in use for the selected building.');
            }

            $buildingLevel->update([
                'level_name' => $validated['level_name'],
                'description' => $validated['description'] ?? null,
                'level_number' => $validated['level_number'],
                'organization_id' => $organization_id,
                'updated_at' => now(),
            ]);

            if ($portal === 'owner') {
                $building = Building::find($buildingLevel->building_id);
                if ($building && $building->status === 'Approved') {
                    $building->update(['status' => 'For Re-Approval']);
                }
            }

            $notificationData = [
                'organization_id' => $organization_id,
                'building_id' => $buildingLevel->building_id,
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

            DB::commit();
            return redirect()->back()->with('success', 'Building Level updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error fetching building level data: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong! Please try again later.');
        }
    }

}
