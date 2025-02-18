<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingUnitDocument extends Model
{
    use HasFactory;

      /**
     * The table associated with the model.
     *
     * @var string
    */
    protected $table = 'buildingunitdocuments';

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
        'unit_id',
        'document_type',
        'file_path',
        'file_name', 
    ];
}
