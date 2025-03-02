<?php

namespace App\Models;

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
        'cnic',
        'date_of_birth',
        'gender',
        'picture',
        'reset_token',
        'role_id',
        'address_id',
        'status',
    ];

    public $timestamps = true;

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'date_of_birth' => 'date:Y-m-d',
    ];

    // Belongs to Relations
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    // Organization Relations
    public function organization()
    {
        return $this->hasOne(Organization::class, 'owner_id', 'id');
    }
    public function createdOrganizations()
    {
        return $this->hasMany(Organization::class, 'created_by', 'id');
    }
    public function updatedOrganizations()
    {
        return $this->hasMany(Organization::class, 'updated_by', 'id');
    }

    // Building Relations
    public function createdBuildings()
    {
        return $this->hasMany(Building::class, 'created_by', 'id');
    }
    public function updatedBuildings()
    {
        return $this->hasMany(Building::class, 'updated_by', 'id');
    }

    // Level Relations
    public function createdLevels()
    {
        return $this->hasMany(BuildingLevel::class, 'created_by', 'id');
    }
    public function updatedLevels()
    {
        return $this->hasMany(BuildingLevel::class, 'updated_by', 'id');
    }

    // Units Relations
    public function createdUnits()
    {
        return $this->hasMany(BuildingUnit::class, 'created_by', 'id');
    }
    public function updatedUnits()
    {
        return $this->hasMany(BuildingUnit::class, 'updated_by', 'id');
    }

    // User Units Relations
    public function userUnits()
    {
        return $this->hasMany(UserBuildingUnit::class, 'user_id', 'id');
    }
    public function createdUserUnits()
    {
        return $this->hasMany(UserBuildingUnit::class, 'created_by', 'id');
    }
    public function updatedUserUnits()
    {
        return $this->hasMany(UserBuildingUnit::class, 'updated_by', 'id');
    }

    // User & Role Permission Relations
    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class, 'user_id', 'id');
    }
    public function grantedUserPermissions()
    {
        return $this->hasMany(UserPermission::class, 'granted_by', 'id');
    }
    public function grantedRolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'granted_by', 'id');
    }

    // Favorites Relations
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id', 'id');
    }

    // Department  Relations
    public function createdDepartments()
    {
        return $this->hasMany(Department::class, 'created_by', 'id');
    }
    public function updatedDepartments()
    {
        return $this->hasMany(Department::class, 'updated_by', 'id');
    }

    // Staff Member Relations
    public function staffMember()
    {
        return $this->hasOne(StaffMember::class, 'user_id', 'id');
    }
    public function createdStaffMembers()
    {
        return $this->hasMany(StaffMember::class, 'created_by', 'id');
    }
    public function updatedStaffMembers()
    {
        return $this->hasMany(StaffMember::class, 'updated_by', 'id');
    }

    // Query Relations
    public function queries()
    {
        return $this->hasMany(Query::class, 'user_id', 'id');
    }

    // JWT Related
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id' => $this->id,
                'role_id' => $this->role_id,
                'role_name' => $this->role->name,
                'organization_id' => $this->getOrganizationId(),
            ]
        ];
    }
    private function getOrganizationId()
    {
        if (!$this->role) {
            return null;
        }

        switch ($this->role->name) {
            case 'Staff':
                return optional($this->staffMember)->organization_id;
            case 'Owner':
                return optional($this->organization)->id;
            default:
                return null;
        }
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
