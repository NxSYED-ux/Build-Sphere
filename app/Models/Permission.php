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

    // Belongs to Relations
    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id', 'id');
    }

    // Has Many Relations
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id', 'id');
    }
    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class, 'permission_id', 'id');
    }
    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id', 'id');
    }
}
