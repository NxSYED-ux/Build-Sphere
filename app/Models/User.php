<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
 
    protected $table = 'users';
    protected $primaryKey = 'id'; 
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_no',
        'address',
        'cnic',
        'gender',
        'picture',
        'description',
        'role_id', 
        'address_id',
        'status',
        'date_of_birth',
    ];
 
    protected $hidden = [
        'password', 
    ];
 
    protected function casts(): array
    {
        return [ 
            'password' => 'hashed',
            'date_of_birth' => 'datetime', 
        ];
    }

    /**
     * Define relationships with the their models.
    */
    public function role() { return $this->belongsTo(Role::class, 'role_id', 'id'); } 
 
    public function address() { return $this->belongsTo(Address::class, 'address_id', 'id'); }

    // 
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'role' => $this->role->name,
            'gender' =>  $this->gender,
        ];
    }

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
