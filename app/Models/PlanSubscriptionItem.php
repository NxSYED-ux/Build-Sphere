<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanSubscriptionItem extends Model
{
    use HasFactory;

    protected $table = 'plansubscriptionitems';

    protected $fillable = [
        'organization_id',
        'subscription_id',
        'quantity',
        'service_catalog_id',
        'used',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];


    // Belongs to Relations
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'id');
    }

    public function serviceCatalog()
    {
        return $this->belongsTo(PlanServiceCatalog::class, 'service_catalog_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
