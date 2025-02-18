<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class DropdownType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dropdowntypes';

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
        'type_name',
        'description',
        'parent_type_id',
        'status', 
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true; 

    /**
     * Define a relationship to fetch the parent type.
     */
    public function parent()
    {
        return $this->belongsTo(DropdownType::class, 'parent_type_id');
    }

    /**
     * Define a relationship to fetch child types.
     */
    public function childs()
    {
        return $this->hasMany(DropdownType::class, 'parent_type_id');
    }

    /**
     * Define a relationship to fetch values associated with this type.
     */
    public function values()
    {
        return $this->hasMany(DropdownValue::class, 'dropdown_type_id');
    }
}
