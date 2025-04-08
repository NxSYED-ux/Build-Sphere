<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPicture extends Model
{
    use HasFactory;

    protected $table = 'unitPictures';

    protected $primaryKey = 'id';

    protected $fillable = [
        'unit_id',
        'file_path',
        'file_name',
    ];

    public $timestamps = true;

    public function unit()
    {
        return $this->belongsTo(BuildingUnit::class, 'unit_id', 'id');
    }
}
