<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'status',

        'updated_at',
    ];

    public $timestamps = true;

    // Has Many Relations
    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id', 'id');
    }

}
