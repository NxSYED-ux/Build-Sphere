<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;
use App\Models\Permission;
use App\Models\UserPermission;
use App\Models\RolePermission;

class ValidatePermission
{
    public function handle(Request $request, Closure $next, $requiredPermissionName, $ignoreUserPermission = true)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->handleResponse($request, 'Unauthorized: Invalid user data', 401);
            }

            $permission = Permission::where('name', $requiredPermissionName)
                ->where('status', 1)
                ->first(['id']);

            if (!$permission) {
                return $this->handleResponse($request, "Access Denied: The permission for \"$requiredPermissionName\" is either inactive or does not exist.", 400);
            }

            $permissionId = $permission->id;

            $promises = [];

            if (!$ignoreUserPermission) {
                $promises[] = new Promise(function () use (&$promises, $user, $permissionId) {
                    $promises[0]->resolve(
                        UserPermission::where('user_id', $user->id)
                            ->where('permission_id', $permissionId)
                            ->where('status', 1)
                            ->exists()
                    );
                });
            }

            $promises[] = new Promise(function () use (&$promises, $user, $permissionId, $ignoreUserPermission) {
                $index = $ignoreUserPermission ? 0 : 1;
                $promises[$index]->resolve(
                    RolePermission::where('role_id', $user->role_id)
                        ->where('permission_id', $permissionId)
                        ->where('status', 1)
                        ->exists()
                );
            });

            $results = Utils::unwrap($promises);
            $hasPermission = $results[0] || ($results[1] ?? false);

            if ($hasPermission) {
                return $next($request);
            }

            return $this->handleResponse($request, 'Access denied: insufficient permissions', 403);
        } catch (\Exception $e) {
            Log::error('Permission validation error: ' . $e->getMessage());
            return $this->handleResponse($request, 'Internal server error', 500);
        }
    }

    private function handleResponse(Request $request, $message, $statusCode)
    {
        return $request->wantsJson()
            ? response()->json(['error' => $message], $statusCode)
            : redirect('/login')->with('error', $message);
    }
}
