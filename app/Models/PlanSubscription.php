<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanSubscription extends Model
{
    use HasFactory;

    protected $table = 'planSubscriptions';

    protected $fillable = [
        'user_id',
        'organization_id',
        'plan_id',
        'type',
        'billing_cycle',
        'stripe_customer_id',
        'stripe_status',
        'stripe_price',
        'currency',
        'trial_ends_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
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

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    // Has Many Relations
    public function subscriptionItems()
    {
        return $this->hasMany(PlanSubscriptionItem::class, 'subscription_id', 'id');
    }
}
