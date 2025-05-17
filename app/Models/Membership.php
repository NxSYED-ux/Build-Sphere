<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'building_id',
        'organization_id',
        'image',
        'name',
        'url',
        'description',
        'category',
        'duration_months',
        'scans_per_day',
        'mark_as_featured',
        'currency',
        'price',
        'original_price',
        'status',
        'created_by',
        'updated_by',
    ];

    // Belongs to Relations
    public function unit()
    {
        return $this->belongsTo(BuildingUnit::class, 'unit_id', 'id');
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
    public function membershipUsers()
    {
        return $this->hasMany(MembershipUser::class, 'membership_id', 'id');
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
