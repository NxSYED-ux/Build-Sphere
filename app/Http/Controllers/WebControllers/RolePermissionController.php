<?php

namespace App\Http\Controllers\WebControllers;

use App\Events\RolePermissionUpdated;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolePermissionController extends Controller
{
    public function showRolePermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'nullable|integer|exists:roles,id',
        ]);

        try {
            $roles = Role::select('id', 'name')->get();
            $permissions = collect();

            $roleId = $request->role_id ?? ($roles->isNotEmpty() ? $roles->first()->id : null);

            if ($roleId) {
                $permissionService = new PermissionService();
                $permissions = $permissionService->getRolePermissionsWithChildren($roleId);
            }

            return view('Heights.Admin.RolePermissions.index', compact('roles', 'permissions', 'roleId'));
        } catch (\Throwable $e) {
            Log::error('Failed to retrieve role permissions: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching role permissions.');
        }
    }

    public function toggleRolePermission(Request $request)
    {
        $request->validate([
            'permission_id' => 'required|integer|exists:permissions,id',
            'role_id' => 'required|integer|exists:roles,id',
            'status' => 'required|in:0,1',
        ]);

        DB::beginTransaction();

        try {

            $user = $request->user();

            RolePermission::where([
                ['role_id', '=', $request->role_id],
                ['permission_id', '=', $request->permission_id],
            ])->update([
                'status' => $request->status,
                'granted_by' => $user->id,
                'updated_at' => now()
            ]);

            event(new RolePermissionUpdated($request->role_id, $request->permission_id, $request->status));

            if ($request->status == 0) {
                $this->disableChildPermissions($request->role_id, $request->permission_id);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Permission updated successfully']);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to update role permissions: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating permission'], 500);
        }
    }

    private function disableChildPermissions($roleId, $parentId)
    {
        $childPermissions = Permission::where('parent_id', $parentId)->pluck('id');

        if ($childPermissions->isNotEmpty()) {
            RolePermission::whereIn('permission_id', $childPermissions)
                ->where('role_id', $roleId)
                ->update([
                    'status' => 0,
                    'updated_at' => now(),
                ]);
        }
    }

}
