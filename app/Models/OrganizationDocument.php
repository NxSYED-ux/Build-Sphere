<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationDocument extends Model
{
    use HasFactory;

    protected $table = 'organizationDocuments';

    protected $primaryKey = 'id';

    protected $fillable = [
        'organization_id',
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

    // Belongs to relations
    public function organization(){
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
