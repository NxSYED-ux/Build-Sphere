<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidatePermission
{
    public function handle(Request $request, Closure $next, $requiredPermissionName, $ignoreUserPermission = true)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->handleResponse($request, 'Unauthorized: Invalid user data', 401);
            }

            $permission = DB::selectOne("SELECT id FROM permissions WHERE name = ? AND status = 1 LIMIT 1", [$requiredPermissionName]);
            if (!$permission) {
                return $this->handleResponse($request, "Access Denied: The permission for \"$requiredPermissionName\" is either inactive or does not exist.", 400);
            }

            $permissionId = $permission->id;

            $exists = DB::selectOne("
                SELECT
                    EXISTS (SELECT 1 FROM userpermissions WHERE user_id = ? AND permission_id = ? AND status = 1) AS user_exists,
                    EXISTS (SELECT 1 FROM rolepermissions WHERE role_id = ? AND permission_id = ? AND status = 1) AS role_exists
            ", [$user->id, $permissionId, $user->role_id, $permissionId]);

            if ((!$ignoreUserPermission && $exists->user_exists) || $exists->role_exists) {
                return $next($request);
            }

            return $this->handleResponse($request, "Access denied: You do not have permission for {$requiredPermissionName}", 403);
        } catch (\Exception $e) {
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
