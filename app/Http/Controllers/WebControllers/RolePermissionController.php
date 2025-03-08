<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
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
                $permissions = RolePermission::where('role_id', $roleId)
                    ->with('permission:id,name,header')
                    ->get()
                    ->map(fn ($rolePermission) => [
                        'id' => $rolePermission->permission->id,
                        'name' => $rolePermission->permission->name,
                        'status' => $rolePermission->status,
                        'header' => $rolePermission->permission->header,
                    ])
                    ->groupBy('header');
            }

            return view('Heights.Admin.RolePermissions.index', compact('roles', 'permissions', 'roleId'));
        } catch (\Exception $e) {
            Log::error('Failed to retrieve role permissions: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching role permissions.');
        }
    }

    public function toggleRolePermission(Request $request)
    {
        $user = $request->user() ?? abort(401, 'Unauthorized');

        $request->validate([
            'permission_id' => 'required|integer|exists:permissions,id',
            'role_id' => 'required|integer|exists:roles,id',
            'status' => 'required|in:0,1',
        ]);

        try {
            $updated = RolePermission::where([
                ['role_id', '=', $request->role_id],
                ['permission_id', '=', $request->permission_id],
            ])->update([
                'status' => $request->status,
                'granted_by' => $user->id,
                'updated_at' => now()
            ]);

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Permission updated successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Permission not found or unchanged']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating permission'], 500);
        }
    }
}
