<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function getRolePermissions($roleId)
    {
        $rolePermissions = RolePermission::where('role_id', $roleId)
            ->with('permission:id,name,header')
            ->get()
            ->pluck('permission')
            ->groupBy('header')
            ->map(function ($permissions) {
                return $permissions->map(fn ($perm) => [
                    'id' => $perm->id,
                    'name' => $perm->name
                ]);
            });

        return response()->json($rolePermissions);
    }
}
