<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationDocument extends Model
{
    use HasFactory;

     /**
     * The table associated with the model.
     *
     * @var string
    */
    protected $table = 'organization_documents';

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
        'organization_id',
        'document_type',
        'file_path',
        'file_name', 
    ];
}
