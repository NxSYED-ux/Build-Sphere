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
        'email',
        'phone',
        'logo',
        'owner_id',
        'address_id',
        'status',
        'payment_gateway_name',
        'payment_gateway_merchant_id',
        'is_online_payment_enabled',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    // Belongs to Relations
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
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
    public function documents()
    {
        return $this->hasMany(OrganizationDocument::class, 'organization_id', 'id');
    }
    public function pictures()
    {
        return $this->hasMany(OrganizationPicture::class, 'organization_id', 'id');
    }
    public function buildings()
    {
        return $this->hasMany(Building::class, 'organization_id', 'id');
    }
    public function units()
    {
        return $this->hasMany(BuildingUnit::class, 'organization_id', 'id');
    }
    public function departments()
    {
        return $this->hasMany(Department::class, 'organization_id', 'id');
    }
    public function staffMembers()
    {
        return $this->hasMany(StaffMember::class, 'organization_id', 'id');
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
