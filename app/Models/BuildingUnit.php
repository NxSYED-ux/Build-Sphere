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
        'unit_type',  //'Room, Shop, Apartment, Restaurant, Gym'
        'price',
        'description', 
        'sale_or_rent', //'Sale', 'Rent', 'Not Available'
        'status', //'Approved', 'Rejected'
        'area' ,
        'availability_status', //'available', 'rented', 'sold', 'not available'
        'level_id',
        'organization_id',
    ]; 
 
    public $timestamps = true;
 
    protected $casts = [ 
        'price' => 'decimal:2',
        'area' => 'decimal:2',
    ];

    public function level() { return $this->belongsTo(BuildingLevel::class, 'level_id', 'id'); }

    public function pictures() { return $this->hasMany(UnitPicture::class, 'unit_id', 'id'); }

    public function userUnits() { return $this->hasMany(UserBuildingUnit::class, 'unit_id', 'id'); }

    public function organization() { return $this->belongsTo(Organization::class, 'organization_id'); }

    public function building() { return $this->belongsTo(Building::class, 'building_id'); }

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
 
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }

    /**
     * Boot method for setting created_by and updated_by automatically.
    */
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
