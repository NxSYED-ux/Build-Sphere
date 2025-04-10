<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanServiceCatalog extends Model
{
    use HasFactory;

    protected $table = 'planservicecatalog';

    protected $fillable = [
        'title',
        'keyword',
        'description',
        'is_mandatory',
    ];

    public $timestamps = false;

    public function planServices()
    {
        return $this->hasMany(PlanService::class, 'service_catalog_id');
    }
}

