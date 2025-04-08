<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $table = 'userPermissions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'status',

        'user_id',
        'permission_id',
    ];

    public $timestamps = true;

    // Belongs to Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
        static::saving(function ($model) {
            if ($user = request()->user) {
                $model->granted_by = $user->id;
            }
        });
    }
}
