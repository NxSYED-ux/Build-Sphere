<?php


namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('Heights.Admin.Roles.index',['roles'=>$roles,'permissions'=>$permissions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user;
        $request->validate([
            'name'                   => 'required|string|unique:roles,name',
            'description'            => 'nullable|string',
            'status'                 => 'required|integer|in:0,1',
            'permissions'            => 'required|array',
            'permissions.*.id'       => 'exists:permissions,id',
            'permissions.*.name'     => 'required|string',
            'permissions.*.header'   => 'required|string',
            'permissions.*.selected' => 'sometimes|accepted',
        ]);

        $selectedPermissions = collect($request->permissions)
            ->filter(function ($permission) {
                return isset($permission['selected']);
            })
            ->map(function ($permission) {
                return [
                    'id'     => $permission['id'],
                    'name'   => $permission['name'],
                    'header' => $permission['header'],
                ];
            })->toArray();

        DB::beginTransaction();
        try {
            Log::info('Working 1');
            $role = Role::create([
                'name'        => $request->name,
                'description' => $request->description,
                'status'      => $request->status,
            ]);

            $roleId = $role->id;
            $rolePermissions = collect($selectedPermissions)->map(function ($permission) use ($user, $roleId) {
                return [
                    'role_id'       => $roleId,
                    'permission_id' => $permission['id'],
                    'name'          => $permission['name'],
                    'header'        => $permission['header'],
                    'granted_by'    => $user->id,
                ];
            })->toArray();

            Log::info('Working 2');
            if (!empty($rolePermissions)) {
                RolePermission::insert($rolePermissions);
            }

            Log::info('Working 3');
            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the role.');
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        return response()->json($role);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $role = Role::findOrFail($id);
        if ($role) {
            // Returning role details wrapped in an object with key 'role'
            return response()->json(['role' => $role]);
        }
        return response()->json(['message' => 'Role not found'], 404);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        if (!$role) {
            return redirect()->route('roles.index')->with('error', 'Role not found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $id . ',id',
            'description' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $role->update($validator->validated());

        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }

    public function updateRole(Request $request)
    {
        $user = $request->user;
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*.id' => 'exists:permissions,id',
            'permissions.*.name' => 'required|string',
            'permissions.*.header' => 'required|string',
        ]);
        $roleId = $request->role_id;

        $selectedPermissions = collect($request->permissions)
            ->where('selected', true)
            ->map(function ($permission) {
                return [
                    'id' => $permission['id'],
                    'name' => $permission['name'],
                    'header' => $permission['header']
                ];
            })->toArray();

        DB::beginTransaction();
        try {
            Log::info('Working 1');
            RolePermission::where('role_id', $roleId)->delete();


            $rolePermissions = collect($selectedPermissions)->map(function ($permission) use ($user, $roleId) {
                return [
                    'role_id' => $roleId,
                    'permission_id' => $permission['id'],
                    'name' => $permission['name'],
                    'header' => $permission['header'],
                    'granted_by' => $user->id,
                ];
            })->toArray();

            Log::info('Working 2');
            if (!empty($rolePermissions)) {
                RolePermission::insert($rolePermissions);
            }

            Log::info('Working 3');
            DB::commit();
            return response()->json(['message' => 'Role updated successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Not Working : '.$e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
}
