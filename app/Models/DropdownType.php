<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropdownType extends Model
{
    use HasFactory;

    protected $table = 'dropdowntypes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'type_name',
        'description',
        'parent_type_id',
        'status',
    ];

    public $timestamps = true;

    // Belongs to Relations
    public function parent()
    {
        return $this->belongsTo(DropdownType::class, 'parent_type_id');
    }

    //Has Many Relations:
    public function childs()
    {
        return $this->hasMany(DropdownType::class, 'parent_type_id');
    }
    public function values()
    {
        return $this->hasMany(DropdownValue::class, 'dropdown_type_id');
    }
}
