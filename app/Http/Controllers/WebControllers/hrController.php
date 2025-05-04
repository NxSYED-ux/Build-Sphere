<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Building;
use App\Models\Department;
use App\Models\DropdownType;
use App\Models\ManagerBuilding;
use App\Models\RolePermission;
use App\Models\StaffMember;
use App\Models\User;
use App\Models\UserPermission;
use App\Notifications\CredentialsEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Customer;
use Stripe\Stripe;


class hrController extends Controller
{
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

            $managerBuildingIds = [];
            if ($role_name === 'Manager' && $roleId !== 3) {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
            }

            $staffQuery = StaffMember::where('organization_id', $organization_id)
                ->whereHas('user', function ($query) use ($roleId, $search) {
                    $query->where('role_id', $roleId);

                    if (!empty($search)) {
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                    }
                })
                ->when($selectedDepartment, fn($query) => $query->where('department_id', $selectedDepartment))
                ->with('user');

            if ($role_name === 'Manager') {
                $staffQuery->whereIn('building_id', $managerBuildingIds);
                if ($selectedBuilding && in_array($selectedBuilding, $managerBuildingIds)) {
                    $staffQuery->where('building_id', $selectedBuilding);
                }
            } elseif ($selectedBuilding) {
                $staffQuery->where('building_id', $selectedBuilding);
            }

            $staffMembers = $staffQuery->paginate(12);

            if ($roleId === 4) {
                $departments = Department::where('organization_id', $organization_id)
                    ->select('id', 'name')
                    ->get();

                $buildings = Building::where('organization_id', $organization_id)
                    ->when($role_name === 'Manager', fn($q) => $q->whereIn('id', $managerBuildingIds))
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

            $managerBuildingIds = [];
            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
            }

            $departments = Department::where('organization_id', $organization_id)
                ->pluck('name', 'id');

            $buildings = Building::where('organization_id', $organization_id)
                ->when($role_name === 'Manager', fn($q) => $q->whereIn('id', $managerBuildingIds))
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
        Log::info('Staff store request: ' . print_r($request->all(), true));
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
            'date_of_birth' => ['bail', 'nullable', 'date'],

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

        $password = Str::random(8);

        DB::beginTransaction();
        try {
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

            DB::commit();

            $user->notify(new CredentialsEmail(
                $loggedUser->name,
                $loggedUser->email,
                $password
            ));

            return redirect()->route('owner.staff.index')->with('success', 'Staff created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }



}
