<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $table = 'buildings';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'building_type',
        'remarks',
        'status', //1 for approved, 2 for  under_review,3 for rejected, 4 for under processing, 5 for reapproved
        'area',
        'construction_year',
        'address_id',
        'organization_id',
    ];

    public $timestamps = true;

    // Belongs to Relations:
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }


    // Has Many Relations:
    public function pictures()
    {
        return $this->hasMany(BuildingPicture::class, 'building_id', 'id');
    }
    public function documents()
    {
        return $this->hasMany(BuildingDocument::class, 'building_id', 'id');
    }
    public function levels()
    {
        return $this->hasMany(BuildingLevel::class, 'building_id', 'id');
    }
    public function units()
    {
        return $this->hasMany(BuildingUnit::class, 'building_id', 'id');
    }
    public function staffMembers()
    {
        return $this->hasMany(StaffMember::class, 'building_id', 'id');
    }
    public function queries()
    {
        return $this->hasMany(Query::class, 'building_id', 'id');
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
