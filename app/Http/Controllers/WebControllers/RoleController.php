<?php


namespace App\Http\Controllers\WebControllers;

use App\Events\RolePermissionUpdated;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::all();
            return view('Heights.Admin.Roles.index', compact('roles'));
        } catch (\Throwable $exception) {
            Log::error('Roles index error: ' . $exception->getMessage());
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function create()
    {
        try {
            $permissions = Permission::get();
            return view('Heights.Admin.Roles.create', compact('permissions'));
        } catch (\Throwable $e) {
            Log::error('Roles create error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'required|integer|exists:permissions,id',
        ] , [
            'permissions.required' => 'At least one permission must be assigned to create the role.',
            'permissions.min' => 'At least one permission must be assigned to create the role.',
            'permissions.*.exists' => 'One or more selected permissions are invalid or may have been deleted while you were creating the role.',
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();

            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $rolePermissions = collect($request->permissions)->map(function ($permissionId) use ($user, $role) {
                return [
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                    'granted_by' => $user->id,
                ];
            })->toArray();

            if (!empty($rolePermissions)) {
                RolePermission::insert($rolePermissions);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Role creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create the role. Please try again.');
        }
    }

    public function edit(string $id)
    {
        try {
            $role = Role::select('id', 'name', 'description', 'updated_at')->findOrFail($id);

            $rolePermissionIds = RolePermission::where('role_id', '=', $role->id)->pluck('permission_id');

            $permissions = Permission::all();

            return view('Heights.Admin.Roles.edit', compact('role', 'permissions', 'rolePermissionIds'));

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not found'], 404);
        } catch (\Throwable $e) {
            Log::error('Failed to retrieve role data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving the role data. Please try again.'], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'role_id' => 'required|integer|exists:roles,id',
            'name' => 'required|string|unique:roles,name,' . $request->role_id . ',id',
            'description' => 'nullable|string',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'required|integer|exists:permissions,id',
            'updated_at' => 'required'
        ], [
            'permissions.required' => 'At least one permission must be assigned to save the role.',
            'permissions.min' => 'At least one permission must be assigned to save the role.',
            'permissions.*.exists' => 'One or more selected permissions are invalid or may have been deleted while you were creating the role.',
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();
            $roleId = $request->role_id;

            $role = Role::where([
                ['id', '=', $roleId],
                ['updated_at', '=', $request->updated_at]
            ])->sharedLock()->first();

            if (!$role) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Please refresh the page and try again.');
            }

            $role->update([
                'name' => $request->name,
                'description' => $request->description,
                'updated_at' => now()
            ]);

            $existingPermissions = RolePermission::where('role_id', $roleId)->pluck('permission_id')->toArray();
            $newPermissions = $request->permissions;

            $permissionsToAdd = array_diff($newPermissions, $existingPermissions);
            $permissionsToRemove = array_diff($existingPermissions, $newPermissions);

            if (!empty($permissionsToRemove)) {
                RolePermission::where('role_id', $roleId)
                    ->whereIn('permission_id', $permissionsToRemove)
                    ->delete();

                foreach ($permissionsToRemove as $permissionId) {
                    event(new RolePermissionUpdated($role->id, $permissionId,0));
                }
            }

            if (!empty($permissionsToAdd)) {
                $rolePermissions = array_map(fn($id) => [
                    'role_id' => $roleId,
                    'permission_id' => $id,
                    'granted_by' => $user->id,
                ], $permissionsToAdd);

                RolePermission::insert($rolePermissions);

                foreach ($permissionsToAdd as $permissionId) {
                    event(new RolePermissionUpdated($roleId, $permissionId, 1));
                }
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role updated successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Role update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update the role. Please try again.');
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $role = Role::find($id);
            if (!$role) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Role not Found');
            }

            if ($role->users()->exists()) {
                DB::rollBack();
                return redirect()->back()->with('error', 'This role cannot be deleted because it is assigned to existing users.');
            }

            RolePermission::where('role_id', $role->id)->delete();
            $role->delete();

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role deleted successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Role deletion failed for ID {$id}: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the role. Please try again.');
        }
    }

}
