<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class Organization extends Model
{
    use HasFactory;
 
    protected $table = 'organizations';  
    protected $primaryKey = 'id'; 
 
    protected $fillable = [
        'name',
        'owner_id',
        'address_id',
        'status',
        'membership_start_date',
        'membership_end_date', 
    ];
 
    public $timestamps = true;
 
    protected $casts = [
        'membership_start_date' => 'datetime',
        'membership_end_date' => 'datetime', 
    ];

    /* Relations */

    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }

    public function address() { return $this->belongsTo(Address::class, 'address_id', 'id'); }

    public function pictures() { return $this->hasMany(OrganizationPicture::class, 'organization_id', 'id'); } 

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
 
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }

    // 
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
