<?php


namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('Heights.Admin.Roles.index',['roles'=>$roles]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create a new role permission
        Role::create($validator->validated());

        return redirect()->route('roles.index')->with('success', 'Role Created successfully');
    }

    public function show(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        return response()->json($role);
    }

    public function edit(string $id)
    {

        $role = Role::findOrFail($id);
        if ($role) {
            // Returning role details wrapped in an object with key 'role'
            return response()->json(['role' => $role]);
        }
        return response()->json(['message' => 'Role not found'], 404);

    }

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
