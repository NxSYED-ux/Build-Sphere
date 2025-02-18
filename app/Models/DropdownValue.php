<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class DropdownValue extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dropdownvalues';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'value_name',
        'description',
        'dropdown_type_id',
        'parent_value_id',
        'status', 
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true; 

    /**
     * Define a relationship to fetch the dropdown type associated with this value.
     */
    public function type()
    {
        return $this->belongsTo(DropdownType::class, 'dropdown_type_id');
    }

    /**
     * Define a relationship to fetch the parent value.
     */
    public function parent()
    {
        return $this->belongsTo(DropdownValue::class, 'parent_value_id');
    }

    /**
     * Define a relationship to fetch child values.
     */
    public function childs()
    {
        return $this->hasMany(DropdownValue::class, 'parent_value_id');
    }
}
