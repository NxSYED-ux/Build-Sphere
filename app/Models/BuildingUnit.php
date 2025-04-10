<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingUnit extends Model
{
    use HasFactory;

    protected $table = 'buildingunits';

    protected $primaryKey = 'id';

    protected $fillable = [
        'unit_name',
        'unit_type',
        'price',
        'description',
        'sale_or_rent',
        'status',
        'area' ,
        'availability_status',

        'level_id',
        'organization_id',
        'building_id',

        'updated_at',
    ];

    public $timestamps = true;

    protected $casts = [
        'price' => 'decimal:2',
        'area' => 'decimal:2',
    ];

    //Belongs to Relations
    public function level()
    {
        return $this->belongsTo(BuildingLevel::class, 'level_id', 'id');
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
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


    //Has Many Relations
    public function userUnits()
    {
        return $this->hasMany(UserBuildingUnit::class, 'unit_id', 'id');
    }
    public function pictures()
    {
        return $this->hasMany(UnitPicture::class, 'unit_id', 'id');
    }
    public function documents()
    {
        return $this->hasMany(UnitDocument::class, 'unit_id', 'id');
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'unit_id', 'id');
    }
    public function queries()
    {
        return $this->hasMany(Query::class, 'unit_id', 'id');
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
