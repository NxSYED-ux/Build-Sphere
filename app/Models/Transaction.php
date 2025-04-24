<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_title',
        'transaction_category',
        'buyer_id',
        'buyer_type',
        'buyer_transaction_type',
        'seller_id',
        'seller_type',
        'seller_transaction_type',
        'building_id',
        'unit_id',
        'payment_method',
        'gateway_payment_id',
        'price',
        'currency',
        'status',
        'is_subscription',
        'billing_cycle',
        'subscription_start_date',
        'subscription_end_date',
        'source_id',
        'source_name',
    ];

    protected $casts = [
        'subscription_start_date' => 'datetime',
        'subscription_end_date' => 'datetime',
        'is_subscription' => 'boolean',
    ];

    public function buyer(): MorphTo
    {
        return $this->morphTo(null, 'buyer_type', 'buyer_id');
    }

    public function seller(): MorphTo
    {
        return $this->morphTo(null, 'seller_type', 'seller_id');
    }

    public function source(): MorphTo
    {
        return $this->morphTo(null, 'source_id', 'source_name');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(BuildingUnit::class);
    }
}
