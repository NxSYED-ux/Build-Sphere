<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBuildingUnit extends Model
{
    use HasFactory;

    protected $table = 'userbuildingunits';
 
    protected $primaryKey = 'id';
 
    protected $fillable = [
        'user_id',
        'unit_id',
        'rent_start_date',
        'rent_end_date',  
        'purchase_date',
        'contract_status', 
    ]; 
 
    public $timestamps = true; 

    protected $casts = [
        'rent_start_date' => 'datetime', 
        'rent_end_date' => 'datetime', 
        'purchase_date' => 'datetime', 
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id'); }

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
