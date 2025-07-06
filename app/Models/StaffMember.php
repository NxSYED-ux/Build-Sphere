<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    use HasFactory;

    protected $table = 'staffmembers';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'department_id',
        'building_id',
        'organization_id',

        'salary',
        'joined_at',
        'active_load',
        'accept_queries',
        'status',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];


    // Belongs to Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    // Has Many Relations
    public function queries()
    {
        return $this->hasMany(Query::class, 'staff_member_id', 'id');
    }

    public function managerBuildings()
    {
        return $this->hasMany(ManagerBuilding::class, 'staff_id', 'id');
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
