<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\UserPermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidatePermission
{
    public function handle(Request $request, Closure $next, $requiredPermissionName)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->handleResponse($request, 'Unauthorized', 403);
            }

            if ($user->is_super_admin === 1) {
                return $next($request);
            }

            $permission = Permission::where('name', $requiredPermissionName)
                ->where('status', 1)
                ->first();

            if (!$permission) {
                return $this->handleResponse(
                    $request,
                    "Access Denied: The permission for \"$requiredPermissionName\" is either inactive or does not exist.",
                    403
                );
            }

            $permissionId = $permission->id;

            $userPermission = UserPermission::where('user_id', $user->id)
                ->where('permission_id', $permissionId)
                ->value('status');

            $rolePermission = null;
            if (is_null($userPermission)) {
                $rolePermission = RolePermission::where('role_id', $user->role_id)
                    ->where('permission_id', $permissionId)
                    ->value('status');
            }

            if ($userPermission === 1 || $rolePermission === 1) {
                return $next($request);
            }

            return $this->handleResponse(
                $request,
                "Access denied: You do not have permission for {$requiredPermissionName}",
                403
            );

        } catch (\Throwable $e) {
            Log::error("Permission validation error for user {$user->id} on permission '{$requiredPermissionName}': " . $e->getMessage());
            return $this->handleResponse($request, 'Internal server error', 500);
        }
    }

    private function handleResponse(Request $request, $message, $statusCode)
    {
        return $request->wantsJson()
            ? response()->json(['error' => $message], $statusCode)
            : redirect()->back()->with('error', $message);

    }
}
