<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerBuilding extends Model
{
    use HasFactory;

    protected $table = 'managerBuildings';

    protected $fillable = [
        'user_id',
        'staff_id',
        'building_id',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(StaffMember::class, 'staff_id');
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
