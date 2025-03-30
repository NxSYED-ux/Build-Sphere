<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserPermissionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('userPermissions.' . $this->userId);
    }

    public function broadcastWith()
    {
        try {
            $permissions = $this->listOfPermissions();
            return ['permissionsList' => $permissions];
        } catch (\Exception $e) {
            Log::error("UserPermissionUpdated broadcastWith failed: " . $e->getMessage());
            return [];
        }
    }



    private function listOfPermissions(){
        try{
            $user = User::find($this->userId);

            $permissions = DB::select("
                SELECT perm.name, perm.header
                FROM permissions perm
                LEFT JOIN userpermissions userPerm
                    ON perm.id = userPerm.permission_id AND userPerm.user_id = ?
                LEFT JOIN rolepermissions rolePerm
                    ON perm.id = rolePerm.permission_id AND rolePerm.role_id = ?
                WHERE COALESCE(userPerm.status, rolePerm.status) = 1
            ", [$user->id, $user->role_id]);

            $permissionNames = collect($permissions)->groupBy('header')->map(function ($group) {
                return $group->pluck('name')->toArray();
            })->toArray();

            return $permissionNames;

        }catch (\Exception $e){
            Log::error('Unable to retrieve permissions (User Permission Event): ' . $e->getMessage());
            return [];
        }
    }

}
