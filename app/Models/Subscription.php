<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'customer_payment_id',
        'user_id',
        'organization_id',
        'building_id',
        'unit_id',
        'source_id',
        'source_name',
        'billing_cycle',
        'subscription_status',
        'price_at_subscription',
        'trial_ends_at',
        'ends_at',
    ];

    protected $dates = [
        'trial_ends_at',
        'ends_at',
    ];

    // Belongs to Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(BuildingUnit::class, 'unit_id', 'id');
    }

    // Polymorphic relationship to source (e.g., Plan, Rental, etc.)
    public function source(): MorphTo
    {
        return $this->morphTo(null, 'source_name', 'source_id');
    }
}
