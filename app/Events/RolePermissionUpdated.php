<?php

namespace App\Events;

use App\Models\Permission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RolePermissionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $roleId;
    protected $permissionId;
    protected $status;

    public function __construct($roleId, $permissionId, $status)
    {
        $this->roleId = $roleId;
        $this->permissionId = $permissionId;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('rolePermissions.' . $this->roleId);
    }

    public function broadcastWith()
    {
        try {
            $permission = $this->retrievePermission();

            if (!$permission) {
                return ['error' => 'Permission not found'];
            }

            return [
                'permissionName' => $permission->name,
                'permissionHeader' => $permission->header,
                'permissionStatus' => $this->status,
            ];
        } catch (\Exception $e) {
            Log::error("rolePermissionsErr broadcastWith failed: " . $e->getMessage());
            return [];
        }
    }

    private function retrievePermission()
    {
        try {
            return Permission::select('name', 'header')
                ->where('id', $this->permissionId)
                ->firstOrFail();
        } catch (\Exception $e) {
            Log::error('Unable to retrieve permissions (Role Permission Event): ' . $e->getMessage());
            return null;
        }
    }
}
