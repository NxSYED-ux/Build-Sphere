<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
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
                $permissions = RolePermission::where('role_id', $roleId)
                    ->whereHas('permission', function ($query) {
                        $query->whereNull('parent_id');
                    })
                    ->with([
                        'permission' => function ($query) use ($roleId) {
                            $query->select('id', 'name', 'header', 'parent_id')
                                ->with(['children' => function ($childQuery) use ($roleId) {
                                    $childQuery->select('id', 'name', 'parent_id')
                                        ->with(['rolePermissions' => function ($rolePermQuery) use ($roleId) {
                                            $rolePermQuery->where('role_id', $roleId)->select('permission_id', 'status');
                                        }]);
                                }]);
                        }
                    ])
                    ->get()
                    ->map(function ($rolePermission) {
                        return [
                            'id' => $rolePermission->permission->id ?? null,
                            'name' => $rolePermission->permission->name ?? 'N/A',
                            'status' => $rolePermission->status,
                            'header' => $rolePermission->permission->header ?? 'No Header',
                            'children' => $rolePermission->permission->children->map(function ($child) {
                                return [
                                    'id' => $child->id,
                                    'name' => $child->name,
                                    'parent_id' => $child->parent_id,
                                    'status' => optional($child->rolePermissions->first())->status ?? 0,
                                ];
                            })
                        ];
                    })
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
            DB::beginTransaction();

            RolePermission::where([
                ['role_id', '=', $request->role_id],
                ['permission_id', '=', $request->permission_id],
            ])->update([
                'status' => $request->status,
                'granted_by' => $user->id,
                'updated_at' => now()
            ]);

            if ($request->status == 0) {
                $this->disableChildPermissions($request->role_id, $request->permission_id);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Permission updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
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
