<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'organization_id',
        'updated_at',
    ];

    public $timestamps = true;

    // Belongs to Relations
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    // Has Many Relations
    public function staffMembers()
    {
        return $this->hasMany(StaffMember::class, 'department_id', 'id');
    }

    public function queries()
    {
        return $this->hasMany(Query::class, 'department_id', 'id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $user = request()->user;

            if ($user) {
                $model->created_by = $user->id;
                $model->updated_by = $user->id;
            }
        });

        static::updating(function ($model) {
            $user = request()->user;
            if ($user) {
                $model->updated_by = $user->id;
            }
        });
    }

}
