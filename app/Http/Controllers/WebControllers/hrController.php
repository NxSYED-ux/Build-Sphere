<?php

namespace App\Http\Controllers\WebControllers;

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
        $user = $request->user() ?? abort(403, 'Unauthorized action.');

        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->with('error', 'This info is for Organization related personals');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];
            $search = $request->input('search');
            $selectedDepartment = $request->input('DepartmentId');
            $selectedBuilding = $request->input('BuildingId');

            $onlyBuildingIds = [];
            if ($role_name === 'Manager' && $roleId !== 3) {
                $onlyBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
            } elseif ($role_name === 'Staff' && $roleId !== 3) {
                $staffRecord = StaffMember::where('user_id', $user->id)->first();
                $onlyBuildingIds = $staffRecord ? [$staffRecord->building_id] : [];
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
                ->when(!empty($onlyBuildingIds), function ($q) use ($onlyBuildingIds) {
                    $q->whereIn('building_id', $onlyBuildingIds);
                })
                ->when($selectedBuilding, function ($q) use ($selectedBuilding) {
                    $q->where('building_id', $selectedBuilding);
                })
                ->with('user')
                ->paginate(12);

            if ($roleId === 4) {
                $departments = Department::where('organization_id', $organization_id)
                    ->select('id', 'name')
                    ->get();

                $buildings = Building::where('organization_id', $organization_id)
                    ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('id', $onlyBuildingIds))
                    ->select('id', 'name')
                    ->get();

                return view('Heights.Owner.HR.Staff.index', compact('staffMembers', 'buildings', 'departments'));
            }

            return view('Heights.Owner.HR.Manager.index', compact('staffMembers'));

        } catch (\Throwable $e) {
            Log::error('Error fetching staff members: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading staff members.');
        }
    }



    // Create Functions & Store Functions
    public function staffCreate(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->with('error', 'This info is for Organization related personals');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $result = $this->checkServiceUsageLimit($organization_id, 3, 'Staff Management', $user->role_id);

            if ($result instanceof RedirectResponse) {
                return $result;
            }

            $onlyBuildingIds = [];
            if ($role_name === 'Manager') {
                $onlyBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
            }
            elseif ($role_name === 'Staff'){
                $staffRecord = StaffMember::where('user_id', $user->id)->first();
                $onlyBuildingIds = [$staffRecord->building_id];
            }

            $departments = Department::where('organization_id', $organization_id)
                ->pluck('name', 'id');

            $buildings = Building::where('organization_id', $organization_id)
                ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('id', $onlyBuildingIds))
                ->pluck('name', 'id');

            $permissions = RolePermission::where('role_id', 4)->get();

            return view('Heights.Owner.HR.Staff.create', compact('departments', 'buildings', 'permissions'));

        } catch (\Exception $e) {
            Log::error('Error in staffCreate: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong while loading the staff creation form.');
        }
    }

    public function staffStore(Request $request)
    {
        $loggedUser = $request->user() ?? abort(403, 'Unauthorized action.');
        $token = $request->attributes->get('token');

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->with('error', 'This info is for Organization related personals');
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        $request->validate([
            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_no' => ['bail', 'nullable', 'string', 'max:20'],
            'cnic' => ['bail', 'nullable', 'max:18', 'unique:users,cnic'],
            'gender' => ['bail', 'nullable', 'in:Male,Female,Other'],
            'date_of_birth' => ['bail', 'required', 'date'],

            'department_id' => ['bail', 'required', 'exists:departments,id'],
            'building_id' => ['bail', 'required', 'exists:buildings,id'],
            'accept_query' => ['bail', 'nullable'],

            'permissions' => ['bail', 'nullable', 'array'],
            'permissions.*' => ['bail', 'nullable', 'integer'],
        ]);

        if (
            $role_name === 'Manager' &&
            !ManagerBuilding::where('building_id', $request->building_id)
                ->where('user_id', $loggedUser->id)
                ->exists()
        ) {
            return redirect()->back()->withInput()->with('error', 'You do not have access to add staff members of the selected building.');
        }
        elseif ($role_name === 'Staff'){
            $staffRecord = StaffMember::where('user_id', $loggedUser->id)->first();

            if (!$staffRecord || $staffRecord->building_id != $request->building_id) {
                return redirect()->back()->withInput()->with('error', 'You do not have access to add staff members of the selected building.');
            }
        }

        $password = Str::random(8);

        DB::beginTransaction();
        try {
            $result = $this->checkServiceUsageLimit($organization_id, 3, 'Staff Management', $loggedUser->role_id);

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
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in Staff Create: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function managerCreate(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->with('error', 'This info is for Organization related personals');
            }

            $organization_id = $token['organization_id'];

            $result = $this->checkServiceUsageLimit($organization_id, 2, 'Managers', $user->role_id);

            if ($result instanceof RedirectResponse) {
                return $result;
            }

            $buildings = Building::where('organization_id', $organization_id)
                ->select('id', 'name')
                ->get();

            $permissions = RolePermission::where('role_id', 3)->get();

            return view('Heights.Owner.HR.Manager.create', compact('buildings', 'permissions'));

        } catch (\Exception $e) {
            Log::error('Error in managerCreate: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong while loading the manager creation form.');
        }
    }

    public function managerStore(Request $request)
    {
        $loggedUser = $request->user() ?? abort(403, 'Unauthorized action.');
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return redirect()->back()->with('error', 'This action is only for Organization owners.');
        }

        $organization_id = $token['organization_id'];

        $request->validate([
            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_no' => ['bail', 'nullable', 'string', 'max:20'],
            'cnic' => ['bail', 'nullable', 'max:18', 'unique:users,cnic'],
            'gender' => ['bail', 'nullable', 'in:Male,Female,Other'],
            'date_of_birth' => ['bail', 'required', 'date'],

            'permissions' => ['bail', 'nullable', 'array'],
            'permissions.*' => ['bail', 'nullable', 'integer'],

            'buildings' => ['bail', 'required', 'array'],
            'buildings.*' => ['bail', 'required', 'integer', 'exists:buildings,id'],
        ]);

        $password = Str::random(8);

        DB::beginTransaction();
        try {
            $result = $this->checkServiceUsageLimit($organization_id, 2, 'Managers', $loggedUser->role_id);

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
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in Manager create: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }



    // Show Functions
    public function staffShow(Request $request, string $id)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->with('error', 'Access denied: This section is restricted to organization personnel only.');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $staffInfo = StaffMember::with('user')->find($id);

            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return redirect()->back()->with('error', 'Invalid staff id');
            }

            $onlyBuildingIds = [];
            if ($role_name === 'Manager') {
                $onlyBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
                $hasAccess = !in_array($staffInfo->building_id, $onlyBuildingIds);

                if (!$hasAccess) {
                    return redirect()->back()->with('error', 'Access denied: You do not manage this building or its staff.');
                }
            }
            elseif ($role_name === 'Staff'){
                $staffRecord = StaffMember::where('user_id', $user->id)->first();

                if (!$staffRecord || $staffRecord->building_id != $staffInfo->building_id) {
                    return redirect()->back()->with('error', 'Access denied: You do not manage this building or its staff.');
                }

                $onlyBuildingIds = [$staffInfo->building_id];
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

            $units = BuildingUnit::where('organization_id', $organization_id)
                ->where('status', 'Approved')
                ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('building_id', $onlyBuildingIds))
                ->select('id', 'unit_name')
                ->get();

            return view('Heights.Owner.HR.Staff.show', compact('staffInfo', 'queries', 'units'));
        } catch (\Exception $e) {
            Log::error('Error in staffShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function managerShow(Request $request, string $id)
    {
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id'])) {
                return redirect()->back()->with('error', 'Access denied: This section is restricted to organization owner only.');
            }

            $organization_id = $token['organization_id'];

            $staffInfo = StaffMember::with('user')->find($id);

            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return redirect()->back()->with('error', 'Invalid manager id');
            }

            $managerBuildings =  ManagerBuilding::where('staff_id', $id)->with('building')->get();

            $buildingIds = $managerBuildings->pluck('building_id')->toArray();
            $transactions = $this->managerBuildingsTransactions($organization_id, $buildingIds);

            return view('Heights.Owner.HR.Manager.show', compact('staffInfo', 'managerBuildings', 'transactions'));
        }catch (\Exception $e) {
            Log::error('Error in managerShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }



    // Edit Functions & Update Functions
    public function staffEdit(Request $request, string $id){
        $user = $request->user() ?? abort(403, 'Unauthorized action.');
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->with('error', 'This info is for Organization related personals');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $staffInfo = StaffMember::with('user')->find($id);

            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return redirect()->back()->with('error', 'Invalid staff id');
            }

            $onlyBuildingIds = [];
            if ($role_name === 'Manager') {
                $onlyBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();

                if (!in_array($staffInfo->building_id, $onlyBuildingIds)) {
                    return redirect()->back()->with('error', 'Access denied: You do not manage this building or its staff.');
                }
            }
            elseif ($role_name === 'Staff'){
                $staffRecord = StaffMember::where('user_id', $user->id)->first();

                if (!$staffRecord || $staffRecord->building_id != $staffInfo->building_id) {
                    return redirect()->back()->with('error', 'Access denied: You do not manage this building or its staff.');
                }
            }

            $departments = Department::where('organization_id', $organization_id)
                ->pluck('name', 'id');

            $buildings = Building::where('organization_id', $organization_id)
                ->when(!empty($onlyBuildingIds), fn($q) => $q->whereIn('id', $onlyBuildingIds))
                ->pluck('name', 'id');


            $userPermissions = UserPermission::where('user_id', $staffInfo->user_id)
                ->select('id', 'permission_id', 'status')
                ->get();

            $excludedPermissionIds = $userPermissions->pluck('permission_id');

            $rolePermissions = RolePermission::where('role_id', 4)
                ->whereNotIn('permission_id', $excludedPermissionIds)
                ->select('id', 'permission_id', 'status')
                ->get();

            $permissions = $userPermissions->concat($rolePermissions)
                ->sortBy('permission_id');

            return view('Heights.Owner.HR.Staff.edit', compact('staffInfo', 'departments', 'buildings', 'permissions'));

        } catch (\Exception $e) {
            Log::error('Error in staff Edit: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function staffUpdate(Request $request)
    {
        $loggedUser = $request->user() ?? abort(403, 'Unauthorized action.');
        $token = $request->attributes->get('token');

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->withInput()->with('error', 'This action is related to organization personals only.');
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

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

        if (
            $role_name === 'Manager' &&
            !ManagerBuilding::where('building_id', $request->building_id)
                ->where('user_id', $loggedUser->id)
                ->exists()
        ) {
            return redirect()->back()->withInput()->with('error', 'You do not have access to add staff members of the selected building.');
        }
        elseif ($role_name === 'Staff'){
            $staffRecord = StaffMember::where('user_id', $loggedUser->id)->first();

            if (!$staffRecord || $staffRecord->building_id != $request->building_id) {
                return redirect()->back()->withInput()->with('error', 'You do not have access to add staff members of the selected building.');
            }
        }

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

            $staff->update([
                'building_id' => $request->building_id,
                'department_id' => $request->department_id,
                'accept_queries' => $request->accept_query ?? 0,
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

            return redirect()->route('owner.staff.index')->with('success', 'Staff updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in staff update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function managerEdit(Request $request, string $id)
    {
        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->with('error', 'This info is for Organization related personals');
            }

            $organization_id = $token['organization_id'];

            $staffInfo = StaffMember::with('user')->find($id);

            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return redirect()->back()->with('error', 'Invalid manager id');
            }

            $buildings = Building::where('organization_id', $organization_id)
                ->select('id', 'name')
                ->get();

            $managerBuildings = ManagerBuilding::where('staff_id', $id)
                ->with('building')
                ->get();

            $userPermissions = UserPermission::where('user_id', $staffInfo->user_id)
                ->select('id', 'permission_id', 'status')
                ->get();

            $excludedPermissionIds = $userPermissions->pluck('permission_id');

            $rolePermissions = RolePermission::where('role_id', 3)
                ->whereNotIn('permission_id', $excludedPermissionIds)
                ->select('id', 'permission_id', 'status')
                ->get();

            $permissions = $userPermissions->concat($rolePermissions)
                ->sortBy('permission_id');

            return view('Heights.Owner.HR.Manager.edit', compact('staffInfo', 'buildings', 'managerBuildings', 'permissions'));

        } catch (\Exception $e) {
            Log::error('Error in manager edit: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function managerUpdate(Request $request)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return redirect()->back()->with('error', 'This action is only for Organization owners.');
        }

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

            return redirect()->route('owner.managers.index')->with('success', 'Manager updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in Manager update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }



    // Promotion
    public function promotionGet(string $id, Request $request)
    {
        try {
            $user = $request->user() ?? abort(403, 'Unauthorized action.');
            $token = $request->attributes->get('token');

            if (empty($token['organization_id'])) {
                return response()->json([
                    'error' => 'This action is only for Organization owners.'
                ], 403);
            }

            $organization_id = $token['organization_id'];

            $staffInfo = StaffMember::where('id', $id)->with('user')->first();
            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return response()->json([
                    'error' => 'Invalid staff Id.'
                ], 400);
            }

            $result = $this->checkServiceUsageLimit($organization_id, 2, 'Managers', $user->role_id, false);

            if (!$result['success']) {
                return response()->json([
                    'plan_upgrade_error' => 'Managers limit exceeded or subscribed plan does not have Managers service in it.'
                ], 403);
            }

            $buildings = Building::where('organization_id', $organization_id)
                ->select('id', 'name')
                ->get();

            $permissions = RolePermission::where('role_id', 3)->with('permission')->get();

            return response()->json([
                'staffInfo' => $staffInfo,
                'buildings' => $buildings,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            Log::error('Error in promotionGet: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function promotion(Request $request)
    {
        $loggedUser = $request->user() ?? abort(403, 'Unauthorized action.');
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return response()->json([
                'error' => 'This action is only for Organization owners.'
            ], 403);
        }

        $organization_id = $token['organization_id'];

        $request->validate([
            'staff_id' => ['bail', 'required', 'exists:staffmembers,id'],
            'permissions' => ['bail', 'nullable', 'array'],
            'permissions.*' => ['bail', 'nullable', 'integer'],
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

            $result = $this->checkServiceUsageLimit($organization_id, 2, 'Managers', $loggedUser->role_id, false);

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
        } catch (\Exception $e) {
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
            $user = $request->user() ?? abort(403, 'Unauthorized action.');
            $token = $request->attributes->get('token');

            if (empty($token['organization_id'])) {
                return response()->json([
                    'error' => 'This action is only for Organization owners.'
                ], 403);
            }

            $organization_id = $token['organization_id'];

            $staffInfo = StaffMember::where('id', $id)->with('user')->first();
            if (!$staffInfo || $staffInfo->organization_id != $organization_id) {
                return response()->json([
                    'error' => 'Invalid staff Id.'
                ], 400);
            }

            $result = $this->checkServiceUsageLimit($organization_id, 3, 'Staff Management', $user->role_id, false);

            if (!$result['success']) {
                return response()->json([
                    'plan_upgrade_error' => 'Staff Management limit exceeded or subscribed plan does not have Staff Management service in it.'
                ], 403);
            }

            $departments = Department::where('organization_id', $organization_id)
                ->pluck('name', 'id');

            $buildings = Building::where('organization_id', $organization_id)
                ->pluck('name', 'id');

            $permissions = RolePermission::where('role_id', 4)->with('permission')->get();

            return response()->json([
                'staffInfo' => $staffInfo,
                'departments' => $departments,
                'buildings' => $buildings,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            Log::error('Error in demotionGet: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function demotion(Request $request)
    {
        $loggedUser = $request->user() ?? abort(403, 'Unauthorized action.');
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return response()->json([
                'error' => 'This action is only for Organization owners.'
            ], 403);
        }

        $organization_id = $token['organization_id'];

        $request->validate([
            'manager_id' => ['bail', 'required', 'exists:staffmembers,id'],
            'building_id' => ['bail', 'required', 'exists:buildings,id'],
            'department_id' => ['bail', 'required', 'exists:departments,id'],
            'accept_query' => ['bail', 'nullable'],
            'permissions' => ['bail', 'nullable', 'array'],
            'permissions.*' => ['bail', 'nullable', 'integer'],
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

            $result = $this->checkServiceUsageLimit($organization_id, 3, 'Staff Management', $loggedUser->role_id, false);

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
        } catch (\Exception $e) {
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

        if (empty($token['organization_id'])) {
            return response()->json(['error' => 'This action is for organization related personals'], 403);
        }

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

        }catch (\Exception $e) {
            Log::error('Error in staffHandleQueries: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }



    // Delete Functions
    public function staffDestroy(Request $request)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return response()->json(['error' => 'This action is related to organization personals'], 403);
        }

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

            return response()->json(['success' => 'Staff deleted successfully.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in staff destroy: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function managerDestroy(Request $request)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return response()->json(['error' => 'This action is related to organization personals'], 403);
        }

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

            return response()->json(['success' => 'Manager deleted successfully.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in manager destroy: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }



    // Helper Function
    public function checkServiceUsageLimit($organization_id, $serviceId, $serviceName, $roleId, bool $redirect = true)
    {
        $errorHeading = $roleId === 2 ? 'plan_upgrade_error' : 'error';

        $subscriptionLimit = PlanSubscriptionItem::where('organization_id', $organization_id)
            ->where('service_catalog_id', $serviceId)
            ->first();

        if (!$subscriptionLimit) {
            $errorMessage = "The current plan doesn't include {$serviceName}. Please upgrade your plan to access this service.";
            return $redirect ? redirect()->back()->with($errorHeading, $errorMessage) : [ 'success' => false];
        }

        if ($subscriptionLimit->used >= $subscriptionLimit->quantity) {
            $errorMessage = "You have reached the {$serviceName} limit. Please upgrade your plan to add more.";
            return $redirect ? redirect()->back()->with($errorHeading, $errorMessage) : [ 'success' => false];
        }

        return [ 'success' => true, 'subscriptionItem' => $subscriptionLimit ];
    }

    public function managerBuildingsTransactions(string $organization_id, $managerBuildings)
    {
        $transactions = Transaction::whereIn('building_id', $managerBuildings)
            ->where(function ($query) use ($organization_id) {
            $query->where(function ($q) use ($organization_id) {
                $q->where('buyer_type', 'organization')
                    ->where('buyer_id', $organization_id);
            })->orWhere(function ($q) use ($organization_id) {
                $q->where('seller_type', 'organization')
                    ->where('seller_id', $organization_id);
            });
        })
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $history = $transactions->map(function ($txn) use ($organization_id) {
            $isBuyer = $txn->buyer_type === 'organization' && $txn->buyer_id == $organization_id;

            return [
                'id' => $txn->id,
                'title' => $txn->transaction_title,
                'type' => $isBuyer ? $txn->buyer_transaction_type : $txn->seller_transaction_type,
                'price' => number_format($txn->price, 2) . ' ' . $txn->currency,
                'status' => $txn->status,
                'created_at' => $txn->created_at->diffForHumans(),
            ];
        });

        return $history;
    }

}
