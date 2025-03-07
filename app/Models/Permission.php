<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'header',
        'description',
        'status',
    ];

    public $timestamps = true;

    // Has Many Relations
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id', 'id');
    }
    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class, 'permission_id', 'id');
    }
}
