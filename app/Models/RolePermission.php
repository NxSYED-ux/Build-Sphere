<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'rolepermissions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'header',
        'status',

        'role_id',
        'permission_id',
    ];

    public $timestamps = true;

    // Belongs to Relations
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }
    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by', 'id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $user = request()->user;

            if ($user) {
                $model->granted_by = $user->id;
            }
        });

        static::updating(function ($model) {
            $user = request()->user;
            if ($user) {
                $model->granted_by = $user->id;
            }
        });
    }
}
