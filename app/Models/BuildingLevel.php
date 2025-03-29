<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingLevel extends Model
{
    use HasFactory;

    protected $table = 'buildinglevels';
    protected $primaryKey = 'id';

    protected $fillable = [
        'level_name',
        'description',
        'level_number',
        'status',  //'approved', 'rejected'

        'building_id',
        'updated_at',
    ];

    public $timestamps = true;

    // Belongs to Relations
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
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
    public function units()
    {
        return $this->hasMany(BuildingUnit::class, 'level_id', 'id');
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
