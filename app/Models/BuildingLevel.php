<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class BuildingLevel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
    */
    protected $table = 'buildinglevels';

    /**
     * The primary key associated with the table.
     *
     * @var string
    */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */
    protected $fillable = [
        'level_name',
        'description',
        'level_number',
        'status',  //'approved', 'rejected'
        'building_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
    */
    public $timestamps = true;

    public function building() { return $this->belongsTo(Building::class, 'building_id', 'id'); }

    public function units() { return $this->hasMany(BuildingUnit::class, 'level_id', 'id'); }

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
