<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanService extends Model
{
    use HasFactory;

    protected $table = 'planservices';

    protected $fillable = [
        'plan_id',
        'service_catalog_id',
        'quantity',
        'status',
    ];

    // Belongs to Relations
    public function serviceCatalog()
    {
        return $this->belongsTo(PlanServiceCatalog::class, 'service_catalog_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Has Many Relations
    public function subscriptionItems()
    {
        return $this->hasMany(PlanSubscriptionItem::class, 'service_id', 'id');
    }

    public function prices()
    {
        return $this->hasMany(PlanServicePrice::class, 'service_id', 'id');
    }

}
