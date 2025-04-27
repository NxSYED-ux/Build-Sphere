<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;

    protected $table = 'queries';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'unit_id',
        'building_id',
        'staff_member_id',
        'department_id',

        'description',
        'status',
        'expected_closure_date',
        'closure_date',
        'remarks',
        'expense'
    ];

    public $timestamps = true;

    protected $casts = [
        'expected_closure_date' => 'datetime',
        'closure_date' => 'datetime',
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
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function staffMember()
    {
        return $this->belongsTo(StaffMember::class, 'staff_member_id', 'id');
    }

    // Has Many Relations
    public function pictures()
    {
        return $this->hasMany(QueryPicture::class, 'query_id', 'id');
    }

}
