<?php


namespace App\Http\Controllers\WebControllers;

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
        } catch (\Exception $exception) {
            Log::error('Roles index error: ' . $exception->getMessage());
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function create()
    {
        try {
            $permissions = Permission::all(['id', 'name']);
            return response()->json([
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            Log::error('Roles create error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching permissions. Please try again.'], 500);
        }
    }

    public function store(Request $request)
    {
        $user = $request->user() ?? abort(401, 'Unauthorized');

        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'required|integer|exists:permissions,id',
        ] , [
            'permissions.required' => 'At least one permission must be assigned to create the role.',
            'permissions.min' => 'At least one permission must be assigned to create the role.',
            'permissions.*.exists' => 'One or more selected permissions are invalid or may have been deleted while you were creating the role.',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
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
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create the role. Please try again.');
        }
    }

    public function show(string $id)
    {
        try {
            $role = Role::select('name', 'description', 'status')->findOrFail($id);
            $rolePermissionIds = RolePermission::where('role_id', $id)->pluck('permission_id');
            $permissions = Permission::whereIn('id', $rolePermissionIds)->get(['id', 'name', 'status']);

            return response()->json([
                'role' => $role,
                'permissions' => $permissions
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not found'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve role data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving the role data. Please try again.'], 500);
        }
    }

    public function edit(string $id)
    {
        try {
            $role = Role::select('id', 'name', 'description', 'status', 'updated_at')->findOrFail($id);
            $rolePermissionIds = RolePermission::where([
                ['role_id', '=', $role->id],
                ['status', '=', '1']
            ])->pluck('permission_id');
            $permissions = Permission::select('id', 'name')->get();

            return response()->json([
                'role' => $role,
                'activePermissionsId' => $rolePermissionIds,
                'permissions' => $permissions
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not found'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve role data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving the role data. Please try again.'], 500);
        }
    }

    public function update(Request $request)
    {
        $user = $request->user() ?? abort(401, 'Unauthorized');
        $request->validate([
            'role_id' => 'required|integer|exists:roles,id',
            'name' => 'required|string|unique:roles,name,' . $request->role_id . ',id',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'required|integer|exists:permissions,id',
            'updated_at' => 'required'
        ], [
            'permissions.required' => 'At least one permission must be assigned to save the role.',
            'permissions.min' => 'At least one permission must be assigned to save the role.',
            'permissions.*.exists' => 'One or more selected permissions are invalid or may have been deleted while you were creating the role.',
        ]);

        $roleId = $request->role_id;

        DB::beginTransaction();
        try {
            $role = Role::where([
                ['id', '=', $roleId],
                ['updated_at', '=', $request->updated_at]
            ])->first();

            if (!$role) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'This role has been updated by another user. Please refresh the page and try again.');
            }

            $role->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
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
            }

            if (!empty($permissionsToAdd)) {
                $rolePermissions = collect($permissionsToAdd)->map(function ($permissionId) use ($user, $roleId) {
                    return [
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                        'granted_by' => $user->id,
                    ];
                })->toArray();

                RolePermission::insert($rolePermissions);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role updated successfully');

        } catch (\Exception $e) {
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
            if (!$role) return redirect()->back()->with('error', 'Role not Found');

            RolePermission::where('role_id', $role->id)->delete();
            $role->delete();

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role deletion failed for ID {$id}: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the role. Please try again.');
        }
    }
}
