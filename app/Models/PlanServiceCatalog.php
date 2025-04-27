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
        'description',
        'parent_id',
        'icon'
    ];

    public $timestamps = false;

    // Has Many Relations
    public function planServices()
    {
        return $this->hasMany(PlanService::class, 'service_catalog_id', 'id');
    }

    public function planSubscriptionItems()
    {
        return $this->hasMany(PlanSubscriptionItem::class, 'service_catalog_id', 'id');
    }
}

