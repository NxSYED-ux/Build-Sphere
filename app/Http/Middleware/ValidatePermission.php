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
    public function handle(Request $request, Closure $next, $requiredPermissionName, $responseType = 'json')
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->handleResponse($responseType, 'Unauthorized: Invalid user data', 401);
            }

            $permission = Permission::where('name', $requiredPermissionName)
                ->where('status', 1)
                ->first(['id']);

            if (!$permission) {
                return $this->handleResponse($responseType, "Access Denied: The permission for \"$requiredPermissionName\" is either inactive or does not exist.", 400);
            }

            $permissionId = $permission->id;

            $userPermissionPromise = new Promise(function () use (&$userPermissionPromise, $user, $permissionId) {
                $userPermissionPromise->resolve(
                    UserPermission::where('user_id', $user->id)
                        ->where('permission_id', $permissionId)
                        ->where('status', 1)
                        ->exists()
                );
            });

            $rolePermissionPromise = new Promise(function () use (&$rolePermissionPromise, $user, $permissionId) {
                $rolePermissionPromise->resolve(
                    RolePermission::where('role_id', $user->role_id)
                        ->where('permission_id', $permissionId)
                        ->where('status', 1)
                        ->exists()
                );
            });

            [$userPermissionExists, $rolePermissionExists] = Utils::unwrap([$userPermissionPromise, $rolePermissionPromise]);

            if ($userPermissionExists || $rolePermissionExists) {
                return $next($request);
            }

            return $this->handleResponse($responseType, 'Access denied: insufficient permissions', 403);
        } catch (\Exception $e) {
            Log::error('Permission validation error: ' . $e->getMessage());
            return $this->handleResponse($responseType, 'Internal server error', 500);
        }
    }

    private function handleResponse($responseType, $message, $statusCode)
    {
        return $responseType === 'json'
            ? response()->json(['error' => $message], $statusCode)
            : redirect('/login')->with('error', $message);
    }
}
