<?php

namespace App\Http\Controllers\WebControllers;

use App\Events\UserPermissionUpdated;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Building;
use App\Models\BuildingUnit;
use App\Models\Department;
use App\Models\ManagerBuilding;
use App\Models\PlanSubscriptionItem;
use App\Models\RolePermission;
use App\Models\StaffMember;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPermission;
use App\Notifications\CredentialsEmail;
use App\Notifications\DatabaseOnlyNotification;
use App\Notifications\UserNotification;
use App\Services\FinanceService;
use App\Services\OwnerFiltersService;
use App\Services\PermissionService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Customer;
use Stripe\Stripe;


class hrController extends Controller
{
    // Index
    public function staffIndex(Request $request){
        return $this->index($request, 4);
    }

    public function managerIndex(Request $request){
        return $this->index($request, 3);
    }

    private function index(Request $request, int $roleId)
    {
        $search = $request->input('search');
        $selectedDepartment = $request->input('DepartmentId');
        $selectedBuilding = $request->input('BuildingId');

        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];
            $buildingIds = [];

            if ($roleId === 4) {
                $ownerServices = new OwnerFiltersService();
                $buildingIds = $ownerServices->getAccessibleBuildingIds();
                $departments = $ownerServices->departments();
                $buildings = $ownerServices->buildings($buildingIds);
            }

            $staffMembers = StaffMember::where('organization_id', $organization_id)
                ->whereHas('user', function ($query) use ($roleId, $search) {
                    $query->where('role_id', $roleId);

                    if (!empty($search)) {
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                    }
                })
                ->when($selectedDepartment, fn($q) => $q->where('department_id', $selectedDepartment))
                ->when($roleId === 4, function ($q) use ($buildingIds) {
                    $q->whereIn('building_id', $buildingIds);
                })
                ->when($selectedBuilding, function ($q) use ($selectedBuilding) {
                    $q->where('building_id', $selectedBuilding);
                })
                ->with('user')
                ->paginate(12);

            if ($roleId === 4) {
                return view('Heights.Owner.HR.Staff.index', compact('staffMembers', 'buildings', 'departments'));
            }

            return view('Heights.Owner.HR.Manager.index', compact('staffMembers'));

        } catch (\Throwable $e) {
            Log::error('Error fetching staff/Managers: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }



    // Create Functions & Store Functions
    public function staffCreate()
    {
        try {
            $subscriptionService = new SubscriptionService();
            $result = $subscriptionService->checkServiceUsageLimit(3, 'Staff Management');

            if ($result instanceof RedirectResponse) {
                return $result;
            }

            $ownerServices = new OwnerFiltersService();
            $buildingIds = $ownerServices->getAccessibleBuildingIds();
            $buildings = $ownerServices->buildings($buildingIds);
            $departments = $ownerServices->departments();

            $permissionService = new PermissionService();
            $permissions = $permissionService->getRolePermissionsWithChildren(4);

            return view('Heights.Owner.HR.Staff.create', compact('departments', 'buildings', 'permissions'));

        } catch (\Throwable $e) {
            Log::error('Error in staffCreate: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading the staff creation form.');
        }
    }

    public function staffStore(Request $request)
    {
        $request->validate([
            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_no' => ['bail', 'required', 'string', 'max:20'],
            'cnic' => ['bail', 'required', 'max:18', 'unique:users,cnic'],
            'gender' => ['bail', 'required', 'in:Male,Female,Other'],
            'date_of_birth' => ['bail', 'required', 'date'],

            'department_id' => ['bail', 'required', 'exists:departments,id'],
            'building_id' => ['bail', 'required', 'exists:buildings,id'],
            'accept_query' => ['bail', 'required'],

            'permissions' => ['bail', 'required', 'array'],
            'permissions.*' => ['bail', 'required', 'integer'],
        ]);

        $password = Str::random(8);

        DB::beginTransaction();
        try {
            $ownerService = new OwnerFiltersService();
            $access = $ownerService->checkBuildingAccess($request->building_id);

            if(!$access['access']){
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', $access['message']);
            }

            $organization_id = $access['organization_id'];

            $subscriptionService = new SubscriptionService();
            $result = $subscriptionService->checkServiceUsageLimit(3, 'Staff Management');

            if ($result instanceof RedirectResponse) {
                DB::rollBack();
                return $result;
            }

            $subscriptionItem = $result['subscriptionItem'];

            Stripe::setApiKey(config('services.stripe.secret'));

            $stripeCustomer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone_no,
            ]);

            $address = Address::create([]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'phone_no' => $request->phone_no,
                'cnic' => $request->cnic,
                'picture' => null,
                'gender' => $request->gender,
                'role_id' => 4,
                'address_id' => $address->id,
                'date_of_birth' => $request->date_of_birth,
                'customer_payment_id' => $stripeCustomer->id,
            ]);

            StaffMember::create([
                'user_id' => $user->id,
                'organization_id' => $organization_id,
                'building_id' => $request->building_id,
                'department_id' => $request->department_id,
                'accept_queries' => $request->accept_query ?? 0,
                'joined_at' => now()
            ]);

            $original = RolePermission::where('role_id', 4)->pluck('status', 'permission_id');
            $new = $request->permissions ?? [];

            $changedPermissions = collect($new)
                ->filter(function ($status, $permissionId) use ($original) {
                    return isset($original[$permissionId]) && $original[$permissionId] != $status;
                });

            foreach ($changedPermissions as $permissionId => $status) {
                UserPermission::create([
                        'user_id' => $user->id,
                        'permission_id' => $permissionId,
                        'status' => $status,
                    ]
                );
            }

            $subscriptionItem->increment('used');

            DB::commit();

            $user->notify(new CredentialsEmail(
                $user->name,
                $user->email,
                $password
            ));

            return redirect()->route('owner.staff.index')->with('success', 'Staff created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in Staff Create: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function managerCreate()
    {
        try {
            $subscriptionService = new SubscriptionService();
            $result = $subscriptionService->checkServiceUsageLimit(2, 'Managers');

            if ($result instanceof RedirectResponse) {
                return $result;
            }

            $ownerServices = new OwnerFiltersService();
            $buildings = $ownerServices->organizationBuildings();

            $permissionService = new PermissionService();
            $permissions = $permissionService->getRolePermissionsWithChildren(3);

            return view('Heights.Owner.HR.Manager.create', compact('buildings', 'permissions'));

        } catch (\Throwable $e) {
            Log::error('Error in managerCreate: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong while loading the manager creation form.');
        }
    }

    public function managerStore(Request $request)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        $request->validate([
            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_no' => ['bail', 'required', 'string', 'max:20'],
            'cnic' => ['bail', 'required', 'max:18', 'unique:users,cnic'],
            'gender' => ['bail', 'required', 'in:Male,Female,Other'],
            'date_of_birth' => ['bail', 'required', 'date'],

            'permissions' => ['bail', 'required', 'array'],
            'permissions.*' => ['bail', 'required', 'integer'],

            'buildings' => ['bail', 'required', 'array'],
            'buildings.*' => ['bail', 'required', 'integer', 'exists:buildings,id'],
        ]);

        $password = Str::random(8);

        DB::beginTransaction();
        try {
            $subscriptionService = new SubscriptionService();
            $result = $subscriptionService->checkServiceUsageLimit(2, 'Managers');

            if ($result instanceof RedirectResponse) {
                DB::rollBack();
                return $result;
            }

            $subscriptionItem = $result['subscriptionItem'];

            Stripe::setApiKey(config('services.stripe.secret'));

            $stripeCustomer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone_no,
            ]);

            $address = Address::create([]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'phone_no' => $request->phone_no,
                'cnic' => $request->cnic,
                'picture' => null,
                'gender' => $request->gender,
                'role_id' => 3,
                'address_id' => $address->id,
                'date_of_birth' => $request->date_of_birth,
                'customer_payment_id' => $stripeCustomer->id,
            ]);

            $staff = StaffMember::create([
                'user_id' => $user->id,
                'organization_id' => $organization_id,
                'accept_queries' => 0,
                'joined_at' => now()
            ]);

            foreach (request('buildings') as $buildingId) {
                ManagerBuilding::create([
                    'user_id' => $user->id,
                    'staff_id' => $staff->id,
                    'building_id' => $buildingId,
                ]);
            }

            $original = RolePermission::where('role_id', 3)->pluck('status', 'permission_id');
            $new = $request->permissions ?? [];

            $changedPermissions = collect($new)
                ->filter(function ($status, $permissionId) use ($original) {
                    return isset($original[$permissionId]) && $original[$permissionId] != $status;
                });

            foreach ($changedPermissions as $permissionId => $status) {
                UserPermission::create([
                        'user_id' => $user->id,
                        'permission_id' => $permissionId,
                        'status' => $status,
                    ]
                );
            }

            $subscriptionItem->increment('used');

            DB::commit();

            $user->notify(new CredentialsEmail(
                $user->name,
                $user->email,
                $password
            ));

            return redirect()->route('owner.managers.index')->with('success', 'Manager created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in Manager create: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again later.');
        }
    }



    // Show Functions
    public function staffShow(Request $request, string $id)
    {
        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $staffInfo = StaffMember::with('user')->find($id);

            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return redirect()->back()->with('error', 'Invalid staff id');
            }

            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();

            $hasAccess = !in_array($staffInfo->building_id, $buildingIds);
            if ($hasAccess) {
                return redirect()->back()->with('error', 'Access denied: You do not manage this building or its staff.');
            }

            $queries = $staffInfo->queries()
                ->when($request->filled('start_date'), function ($query) use ($request) {
                    $query->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->filled('end_date'), function ($query) use ($request) {
                    $query->whereDate('created_at', '<=', $request->end_date);
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->filled('min_expense'), function ($query) use ($request) {
                    $query->where('expense', '>=', $request->min_expense);
                })
                ->when($request->filled('max_expense'), function ($query) use ($request) {
                    $query->where('expense', '<=', $request->max_expense);
                })
                ->when($request->filled('unit'), function ($query) use ($request) {
                    $query->where('unit_id', $request->unit);
                })
                ->paginate(10)
                ->appends($request->query());

            $units = $ownerService->units($buildingIds);

            return view('Heights.Owner.HR.Staff.show', compact('staffInfo', 'queries', 'units'));
        } catch (\Throwable $e) {
            Log::error('Error in staffShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function managerShow(Request $request, string $id)
    {
        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $staffInfo = StaffMember::with('user')->find($id);

            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return redirect()->back()->with('error', 'Invalid manager id');
            }

            $ownerServices = new OwnerFiltersService();
            $managerBuildings = $ownerServices->managerBuildings($staffInfo->id);

            $buildingIds = $managerBuildings->pluck('building_id')->toArray();

            $financeService = new FinanceService();
            $transactions = $financeService->getRecentBuildingTransactions($organization_id, $buildingIds);

            return view('Heights.Owner.HR.Manager.show', compact('staffInfo', 'managerBuildings', 'transactions'));

        } catch (\Throwable $e) {
            Log::error('Error in managerShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }



    // Edit Functions & Update Functions
    public function staffEdit(Request $request, string $id)
    {
        try {
            $currentUserId = $request->user()->id;
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $staffInfo = StaffMember::with('user')->find($id);

            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return redirect()->back()->with('error', 'Invalid staff id');
            }

            if ($staffInfo->user_id == $currentUserId) {
                return redirect()->back()->with('error', 'You cannot edit your own staff record.');
            }

            $ownerServices = new OwnerFiltersService();
            $buildingIds = $ownerServices->getAccessibleBuildingIds();

            $hasAccess = !in_array($staffInfo->building_id, $buildingIds);
            if ($hasAccess) {
                return redirect()->back()->with('error', 'Access denied: You do not manage this building or its staff.');
            }

            $departments = $ownerServices->departments();
            $buildings = $ownerServices->buildings($buildingIds);

            $permissionService = new PermissionService();
            $permissions = $permissionService->getUserPermissionsWithChildren($staffInfo->user_id,4);

            return view('Heights.Owner.HR.Staff.edit', compact('staffInfo', 'departments', 'buildings', 'permissions'));

        } catch (\Throwable $e) {
            Log::error('Error in staff Edit: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function staffUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:staffmembers,id',

            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . StaffMember::find($request->id)->user_id . ',id',
            'phone_no' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:18|unique:users,cnic,' . StaffMember::find($request->id)->user_id . ',id',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'required|date',

            'department_id' => 'required|exists:departments,id',
            'building_id' => 'required|exists:buildings,id',
            'accept_query' => 'nullable',

            'permissions' => 'nullable|array',
            'permissions.*' => 'nullable|integer',

            'updated_at' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $ownerService = new OwnerFiltersService();
            $access = $ownerService->checkBuildingAccess($request->building_id);

            if(!$access['access']){
                DB::rollBack();
                return redirect()->back()->withInput($request->except('unitId'))->with('error', $access['message']);
            }

            $organization_id = $access['organization_id'];


            $staff = StaffMember::where([
                ['id', '=', $request->id],
                ['updated_at', '=', $request->updated_at],
                ['organization_id', '=', $organization_id],
            ])->with('user')->first();

            if (!$staff) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Please refresh the page and try again.');
            }

            $currentUserId = $request->user()->id;
            if ($staff->user_id == $currentUserId) {
                DB::rollBack();
                return redirect()->back()->with('error', 'You cannot update your own staff record.');
            }

            $user = $staff->user;

            $updateJoiningDate = false;
            if ((int) $staff->building_id !== (int) $request->building_id || (int) $staff->department_id !== (int) $request->department_id) {
                $updateJoiningDate = true;
            }

            $staff->update([
                'building_id' => $request->building_id,
                'department_id' => $request->department_id,
                'accept_queries' => $request->accept_query ?? 0,
                'joined_at' => $updateJoiningDate ? now() : $staff->joined_at,
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'cnic' => $request->cnic,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
            ]);

            $original = RolePermission::where('role_id', 4)->pluck('status', 'permission_id');
            $new = $request->permissions ?? [];

            $changedPermissions = collect($new)
                ->filter(function ($status, $permissionId) use ($original) {
                    return isset($original[$permissionId]) && $original[$permissionId] != $status;
                });

            foreach ($changedPermissions as $permissionId => $status) {
                UserPermission::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'permission_id' => $permissionId,
                    ],
                    [
                        'status' => $status,
                    ]
                );
            }

            $permissionIdsToKeep = array_keys($changedPermissions->toArray());

            UserPermission::where('user_id', $user->id)
                ->whereNotIn('permission_id', $permissionIdsToKeep)
                ->delete();

            DB::commit();

            event(new UserPermissionUpdated($user->id));

            return redirect()->route('owner.staff.index')->with('success', 'Staff updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in staff update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function managerEdit(Request $request, string $id)
    {
        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];
            $staffInfo = StaffMember::with('user')->find($id);

            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return redirect()->back()->with('error', 'Invalid manager id');
            }

            $ownerServices = new OwnerFiltersService();
            $managerBuildings = $ownerServices->managerBuildings($staffInfo->id);
            $buildings = $ownerServices->organizationBuildings();

            $permissionService = new PermissionService();
            $permissions = $permissionService->getUserPermissionsWithChildren($staffInfo->user_id,3);

            return view('Heights.Owner.HR.Manager.edit', compact('staffInfo', 'buildings', 'managerBuildings', 'permissions'));

        } catch (\Throwable $e) {
            Log::error('Error in manager edit: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function managerUpdate(Request $request)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        $request->validate([
            'id' => 'required|exists:staffmembers,id',

            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . StaffMember::find($request->id)->user_id . ',id',
            'phone_no' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:18|unique:users,cnic,' . StaffMember::find($request->id)->user_id . ',id',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'required|date',

            'permissions' => 'nullable|array',
            'permissions.*' => 'nullable|integer',

            'buildings' => 'required|array',
            'buildings.*' => 'required|integer|exists:buildings,id',

            'updated_at' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $staff = StaffMember::where([
                ['id', '=', $request->id],
                ['updated_at', '=', $request->updated_at],
                ['organization_id', '=', $organization_id],
            ])->with('user')->first();

            if (!$staff) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Please refresh the page and try again.');
            }

            $user = $staff->user;

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'cnic' => $request->cnic,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
            ]);

            ManagerBuilding::where('staff_id', $staff->id)->delete();

            foreach (request('buildings') as $buildingId) {
                ManagerBuilding::create([
                    'user_id' => $user->id,
                    'staff_id' => $staff->id,
                    'building_id' => $buildingId,
                ]);
            }

            $original = RolePermission::where('role_id', 3)->pluck('status', 'permission_id');
            $new = $request->permissions ?? [];

            $changedPermissions = collect($new)
                ->filter(function ($status, $permissionId) use ($original) {
                    return isset($original[$permissionId]) && $original[$permissionId] != $status;
                });

            foreach ($changedPermissions as $permissionId => $status) {
                UserPermission::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'permission_id' => $permissionId,
                    ],
                    [
                        'status' => $status,
                    ]
                );
            }

            $permissionIdsToKeep = array_keys($changedPermissions->toArray());

            UserPermission::where('user_id', $user->id)
                ->whereNotIn('permission_id', $permissionIdsToKeep)
                ->delete();

            DB::commit();

            event(new UserPermissionUpdated($user->id));

            return redirect()->route('owner.managers.index')->with('success', 'Manager updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in Manager update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }



    // Promotion
    public function promotionGet(string $id, Request $request)
    {
        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $staffInfo = StaffMember::where('id', $id)->with('user')->first();

            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return response()->json([
                    'error' => 'Invalid staff Id.'
                ], 400);
            }

            $subscriptionService = new SubscriptionService();
            $result = $subscriptionService->checkServiceUsageLimit(2, 'Managers', false);

            if (!$result['success']) {
                return response()->json([
                    'plan_upgrade_error' => 'Managers limit exceeded or subscribed plan does not have Managers service in it.'
                ], 403);
            }

            $ownerServices = new OwnerFiltersService();
            $buildings = $ownerServices->organizationBuildings();

            $permissionService = new PermissionService();
            $permissions = $permissionService->getRolePermissionsWithChildren(3);

            return response()->json([
                'staffInfo' => $staffInfo,
                'buildings' => $buildings,
                'permissions' => $permissions
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in promotionGet: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function promotion(Request $request)
    {
        $loggedUser = $request->user();
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        $request->validate([
            'staff_id' => ['bail', 'required', 'exists:staffmembers,id'],
            'permissions' => ['bail', 'required', 'array'],
            'permissions.*' => ['bail', 'required', 'integer'],
            'buildings' => ['bail', 'required', 'array'],
            'buildings.*' => ['bail', 'required', 'integer', 'exists:buildings,id'],
        ]);

        DB::beginTransaction();
        try {
            $staff = StaffMember::where('id', $request->staff_id)
                ->whereHas('user', function ($query) {
                    $query->where('role_id', 4);
                })
                ->with('user')
                ->first();

            if (!$staff || $staff->organization_id != $organization_id) {
                return response()->json([
                    'error' => 'Invalid staff ID or the staff is already promoted as the manager.'
                ], 400);
            }

            $user = $staff->user;

            $subscriptionService = new SubscriptionService();
            $result = $subscriptionService->checkServiceUsageLimit(2, 'Managers', false);

            if (!$result['success']) {
                DB::rollBack();
                return response()->json([
                    'plan_upgrade_error' => 'Managers limit exceeded or subscribed plan does not have Managers service in it.'
                ], 403);
            }

            $subscriptionItem = $result['subscriptionItem'];

            $user->update([
                'role_id' => 3,
            ]);

            $staff->update([
                'department_id' => null,
                'building_id' => null,
                'accept_queries' => 0,
            ]);

            foreach ($request->buildings as $buildingId) {
                ManagerBuilding::create([
                    'user_id' => $user->id,
                    'staff_id' => $staff->id,
                    'building_id' => $buildingId,
                ]);
            }

            UserPermission::where('user_id', $user->id)->delete();

            $originalPermissions = RolePermission::where('role_id', 3)->pluck('status', 'permission_id');
            $new = $request->permissions ?? [];

            $changedPermissions = collect($new)
                ->filter(function ($status, $permissionId) use ($originalPermissions) {
                    return isset($originalPermissions[$permissionId]) && $originalPermissions[$permissionId] != $status;
                });

            foreach ($changedPermissions as $permissionId => $status) {
                UserPermission::create([
                    'user_id' => $user->id,
                    'permission_id' => $permissionId,
                    'status' => $status,
                ]);
            }

            $subscriptionItem->increment('used');

            $subscription = PlanSubscriptionItem::where('organization_id', $organization_id)
                ->where('service_catalog_id', 3)
                ->lockForUpdate()
                ->first();

            if ($subscription && $subscription->used > 0) {
                $subscription->decrement('used');
            }

            DB::commit();

            event(new UserPermissionUpdated($user->id));

            $loggedUser->notify(new DatabaseOnlyNotification(
                $user->picture ?? 'uploads/Notification/Light-theme-Logo.svg',
                'Staff Promoted Successfully',
                "{$user->name} promoted successfully as a manager",
                ['web' => "owner/managers/{$staff->id}/show"]
            ));

            $user->notify(new UserNotification(
                'uploads/Notification/Light-theme-Logo.svg',
                "You're Now a Manager",
                'Congratulations! You have been promoted to a manager.',
                ['web' => "owner/dashboard"]
            ));

            return response()->json([
                'message' => 'Staff promoted successfully.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Staff promotion failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }



    // Demotion
    public function demotionGet(string $id, Request $request)
    {
        try {
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $staffInfo = StaffMember::where('id', $id)->with('user')->first();
            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return response()->json([
                    'error' => 'Invalid staff Id.'
                ], 400);
            }

            $subscriptionService = new SubscriptionService();
            $result = $subscriptionService->checkServiceUsageLimit(3, 'Staff Management', false);

            if (!$result['success']) {
                return response()->json([
                    'plan_upgrade_error' => 'Staff Management limit exceeded or subscribed plan does not have Staff Management service in it.'
                ], 403);
            }

            $ownerServices = new OwnerFiltersService();
            $departments = $ownerServices->departments();
            $buildings = $ownerServices->organizationBuildings();

            $permissionService = new PermissionService();
            $permissions = $permissionService->getRolePermissionsWithChildren(4);

            return response()->json([
                'staffInfo' => $staffInfo,
                'departments' => $departments,
                'buildings' => $buildings,
                'permissions' => $permissions
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in demotionGet: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function demotion(Request $request)
    {
        $loggedUser = $request->user();
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        $request->validate([
            'manager_id' => ['bail', 'required', 'exists:staffmembers,id'],
            'building_id' => ['bail', 'required', 'exists:buildings,id'],
            'department_id' => ['bail', 'required', 'exists:departments,id'],
            'accept_query' => ['bail', 'nullable'],
            'permissions' => ['bail', 'required', 'array'],
            'permissions.*' => ['bail', 'required', 'integer'],
        ]);

        DB::beginTransaction();
        try {
            $staff = StaffMember::where('id', $request->manager_id)
                ->whereHas('user', function ($query) {
                    $query->where('role_id', 3);
                })
                ->with('user')
                ->first();

            if (!$staff || $staff->organization_id != $organization_id) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Invalid manager id or the manager is already demoted as the staff member.'
                ], 400);
            }

            $user = $staff->user;

            $subscriptionService = new SubscriptionService();
            $result = $subscriptionService->checkServiceUsageLimit(3, 'Staff Management', false);

            if (!$result['success']) {
                DB::rollBack();
                return response()->json([
                    'plan_upgrade_error' => 'Staff Management limit exceeded or subscribed plan does not have Staff Management service in it.'
                ], 403);
            }

            $subscriptionItem = $result['subscriptionItem'];

            $user->update([
                'role_id' => 4,
            ]);

            $staff->update([
                'department_id' => $request->department_id,
                'building_id' => $request->building_id,
                'accept_queries' => $request->accept_query ?? 0,
                'joined_at' => now()
            ]);

            ManagerBuilding::where('user_id', $user->id)->delete();
            UserPermission::where('user_id', $user->id)->delete();

            $originalPermissions = RolePermission::where('role_id', 4)->pluck('status', 'permission_id');
            $new = $request->permissions ?? [];

            $changedPermissions = collect($new)
                ->filter(function ($status, $permissionId) use ($originalPermissions) {
                    return isset($originalPermissions[$permissionId]) && $originalPermissions[$permissionId] != $status;
                });

            foreach ($changedPermissions as $permissionId => $status) {
                UserPermission::create([
                    'user_id' => $user->id,
                    'permission_id' => $permissionId,
                    'status' => $status,
                ]);
            }

            $subscriptionItem->increment('used');

            $subscription = PlanSubscriptionItem::where('organization_id', $organization_id)
                ->where('service_catalog_id', 2)
                ->lockForUpdate()
                ->first();

            if ($subscription && $subscription->used > 0) {
                $subscription->decrement('used');
            }

            DB::commit();

            event(new UserPermissionUpdated($user->id));

            $loggedUser->notify(new DatabaseOnlyNotification(
                $user->picture ?? 'uploads/Notification/Light-theme-Logo.svg',
                'Manager Demoted Successfully',
                "{$user->name} demoted successfully as a staff member",
                ['web' => "owner/staff/{$staff->id}/show"]
            ));

            $user->notify(new UserNotification(
                'uploads/Notification/Light-theme-Logo.svg',
                "You're Now a Staff Member",
                'You have been demoted to a staff member.',
                ['web' => ""]
            ));

            return response()->json([
                'message' => 'Manager demoted successfully.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Manager demotion failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }



    // Accept Queries
    public function staffHandleQueries(Request $request)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        $request->validate([
            'id' => 'required|exists:staffmembers,id',
            'accept_query' => 'required|in:0,1',
        ]);

        try {
            $staff = StaffMember::where('id', $request->id)
                ->where('organization_id', $organization_id)
                ->first();

            if (!$staff) {
                return response()->json(['error' => 'Invalid staff id.'], 400);
            }

            $staff->update([
                'accept_queries' => $request->accept_query ?? 0,
            ]);

            return response()->json(['success' => 'Query handling status updated successfully.'], 200);

        }catch (\Throwable $e) {
            Log::error('Error in staffHandleQueries: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }



    // Delete Functions
    public function staffDestroy(Request $request)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        $request->validate([
            'id' => 'required|exists:staffmembers,id',
        ]);

        DB::beginTransaction();

        try {
            $staff = StaffMember::where('id', $request->id)
                ->where('organization_id', $organization_id)
                ->with('user')
                ->first();

            if (!$staff) {
                DB::rollBack();
                return response()->json(['error' => 'Staff not found.'], 404);
            }

            if ($staff->queries()->whereIn('status', ['Open', 'In Progress'])->exists()) {
                DB::rollBack();
                return response()->json(['error' => 'Cannot delete staff with open or in-progress queries.'], 400);
            }

            $user = $staff->user;
            if ($user) {
                $user->update(['role_id' => 5]);
            }

            $staff->delete();

            $subscription = PlanSubscriptionItem::where('organization_id', $organization_id)
                ->where('service_catalog_id', 3)
                ->lockForUpdate()
                ->first();

            if ($subscription && $subscription->used > 0) {
                $subscription->decrement('used');
            }

            DB::commit();

            event(new UserPermissionUpdated($user->id));

            return response()->json(['success' => 'Staff deleted successfully.'], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in staff destroy: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function managerDestroy(Request $request)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        $request->validate([
            'id' => 'required|exists:staffmembers,id',
        ]);

        DB::beginTransaction();

        try {
            $manager = StaffMember::where('id', $request->id)
                ->where('organization_id', $organization_id)
                ->with('user')
                ->first();

            if (!$manager) {
                DB::rollBack();
                return response()->json(['error' => 'Manager not found.'], 404);
            }

            $user = $manager->user;

            if ($user) {
                $user->update(['role_id' => 5]);
            }

            ManagerBuilding::where('staff_id', $manager->id)->delete();
            $manager->delete();

            $subscription = PlanSubscriptionItem::where('organization_id', $organization_id)
                ->where('service_catalog_id', 2)
                ->lockForUpdate()
                ->first();

            if ($subscription && $subscription->used > 0) {
                $subscription->decrement('used');
            }

            DB::commit();

            event(new UserPermissionUpdated($user->id));

            return response()->json(['success' => 'Manager deleted successfully.'], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in manager destroy: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

}
