<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationPicture extends Model
{
    use HasFactory;

    protected $table = 'organizationPictures';

    protected $primaryKey = 'id';

    protected $fillable = [
        'organization_id',
        'file_path',
        'file_name',
    ];

    public $timestamps = true;

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
