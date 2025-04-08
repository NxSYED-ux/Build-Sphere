<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingPicture extends Model
{
    use HasFactory;

    protected $table = 'buildingPictures';

    protected $primaryKey = 'id';

    protected $fillable = [
        'building_id',
        'file_path',
        'file_name',
    ];

    public $timestamps = true;

    // Belongs to Relations
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }
}
