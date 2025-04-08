<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanSubscriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'subscription_id',
        'service_id',
        'service_name',
        'service_keyword',
        'quantity',
        'meta',
        'used',
    ];

    protected $casts = [
        'meta' => 'array',
    ];


    // Belongs to Relations
    public function subscription()
    {
        return $this->belongsTo(PlanSubscription::class, 'subscription_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(PlanService::class, 'service_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
