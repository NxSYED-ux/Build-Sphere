<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitDocument extends Model
{
    use HasFactory;

    protected $table = 'unitDocuments';

    protected $primaryKey = 'id';

    protected $fillable = [
        'unit_id',
        'document_type',
        'file_path',
        'file_name',
        'issue_date',
        'expiry_date',
    ];

    public $timestamps = true;

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Belongs to Relations
    public function unit(){
        return $this->belongsTo(BuildingUnit::class, 'unit_id');
    }

}
