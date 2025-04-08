<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropdownValue extends Model
{
    use HasFactory;

    protected $table = 'dropDownValues';

    protected $primaryKey = 'id';

    protected $fillable = [
        'value_name',
        'description',
        'dropdown_type_id',
        'parent_value_id',
        'status',
    ];

    public $timestamps = true;

    // Belongs to Relations
    public function type()
    {
        return $this->belongsTo(DropdownType::class, 'dropdown_type_id');
    }
    public function parent()
    {
        return $this->belongsTo(DropdownValue::class, 'parent_value_id');
    }

    // Has Many Relations
    public function childs()
    {
        return $this->hasMany(DropdownValue::class, 'parent_value_id');
    }
}
