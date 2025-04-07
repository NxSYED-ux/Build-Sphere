<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'organization_id',
        'type',
        'billing_cycle',
        'stripe_payment_id',
        'amount',
        'currency',
        'status',
        'subscription_start_date',
        'subscription_end_date',
        'metadata',
    ];

    protected $casts = [
        'subscription_start_date' => 'datetime',
        'subscription_end_date' => 'datetime',
        'metadata' => 'array',
    ];

    // Belongs to Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
