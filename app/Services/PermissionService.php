<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\UserPermission;

class PermissionService
{
    public function getRolePermissionsWithChildren(int $roleId)
    {
        return RolePermission::where('role_id', $roleId)
            ->whereHas('permission', function ($query) {
                $query->whereNull('parent_id');
            })
            ->with([
                'permission' => function ($query) use ($roleId) {
                    $query->select('id', 'name', 'header', 'parent_id')
                        ->with(['children' => function ($childQuery) use ($roleId) {
                            $childQuery->select('id', 'name', 'parent_id')
                                ->whereHas('rolePermissions', function ($rolePermQuery) use ($roleId) {
                                    $rolePermQuery->where('role_id', $roleId);
                                })
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

    public function getUserPermissionsWithChildren(int $userId, int $roleId)
    {
        $userPermissionIds = UserPermission::where('user_id', $userId)->pluck('permission_id')->toArray();

        $rolePermissionIds = RolePermission::where('role_id', $roleId)
            ->whereNotIn('permission_id', $userPermissionIds)
            ->pluck('permission_id')
            ->toArray();

        $allPermissionIds = array_unique(array_merge($userPermissionIds, $rolePermissionIds));

        if (empty($allPermissionIds)) {
            return collect();
        }

        $parentPermissions = Permission::whereIn('id', $allPermissionIds)
            ->whereNull('parent_id')
            ->with([
                'userPermissions' => fn($q) => $q->where('user_id', $userId)->select('permission_id', 'status'),
                'rolePermissions' => fn($q) => $q->where('role_id', $roleId)->select('permission_id', 'status'),
                'children' => fn($q) => $q
                    ->whereIn('id', $allPermissionIds)
                    ->with([
                        'userPermissions' => fn($q2) => $q2->where('user_id', $userId)->select('permission_id', 'status'),
                        'rolePermissions' => fn($q2) => $q2->where('role_id', $roleId)->select('permission_id', 'status'),
                    ])
            ])
            ->get();

        return $parentPermissions->map(function ($parent) use ($userId, $roleId) {
            $status = optional($parent->userPermissions->first())->status ?? optional($parent->rolePermissions->first())->status ?? 0;

            return [
                'id' => $parent->id,
                'name' => $parent->name,
                'header' => $parent->header ?? 'No Header',
                'status' => $status,
                'children' => $parent->children->map(function ($child) use ($userId, $roleId) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'parent_id' => $child->parent_id,
                        'status' => optional($child->userPermissions->first())->status ?? optional($child->rolePermissions->first())->status ?? 0,
                    ];
                }),
            ];
        })->groupBy('header');
    }

}
