<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBuildingUnit extends Model
{
    use HasFactory;

    protected $table = 'userBuildingUnits';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'unit_id',
        'type',
        'price',
        'rent_start_date',
        'rent_end_date',
        'purchase_date',
        'contract_status',
    ];

    public $timestamps = true;

    protected $casts = [
        'rent_start_date' => 'date:Y-m-d',
        'rent_end_date' => 'date:Y-m-d',
        'purchase_date' => 'date:Y-m-d',
    ];

    // Belongs to Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function unit()
    {
        return $this->belongsTo(BuildingUnit::class, 'unit_id', 'id');
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
    public function pictures()
    {
        return $this->hasMany(UserUnitPicture::class, 'user_unit_id', 'id');
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
