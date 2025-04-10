<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingDocument extends Model
{
    use HasFactory;

    protected $table = 'buildingdocuments';

    protected $primaryKey = 'id';

    protected $fillable = [
        'building_id',
        'document_type',
        'issue_date',
        'expiry_date',
        'file_path',
        'file_name',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public $timestamps = true;

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }

}
