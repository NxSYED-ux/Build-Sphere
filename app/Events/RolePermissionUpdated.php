<?php

namespace App\Events;

use App\Models\Permission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RolePermissionUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $roleId;
    protected $permissionId;

    public function __construct($roleId, $permissionId)
    {
        $this->roleId = $roleId;
        $this->permissionId = $permissionId;

    }

    public function broadcastOn()
    {
        return new PrivateChannel('role-permissions.' . $this->roleId);
    }

    public function broadcastWith()
    {
        return [
            'updated_permission' => $this->retrievePermission(),
        ];
    }

    private function retrievePermission(){
        try{
            $permission = Permission::where('id', $this->permissionId)
                ->select('name', 'header')
                ->first();

            return $permission;
        }catch (\Exception $e){
            Log::error('Unable to retrieve permissions (Role Permission Event): '.$e->getMessage());
            return null;
        }
    }
}
