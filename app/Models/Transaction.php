<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
        'membership_id',
        'plan_id',

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
        return $this->morphTo(null, 'source_name', 'source_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(BuildingUnit::class, 'unit_id', 'id');
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class, 'membership_id', 'id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    public function scopeFilterTransactions(Builder $query, $request): Builder
    {
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $startDate = $request->filled('start_date') ? Carbon::parse($request->input('start_date')) : now()->subDays(30);
            $endDate = $request->filled('end_date') ? Carbon::parse($request->input('end_date')) : now();
            $query->whereBetween('created_at', [$startDate, $endDate]);

        } else {
            $days = is_numeric($request->input('date_range')) ? (int) $request->input('date_range') : 30;
            $startDate = now()->subDays($days);
            $endDate = now();
            $query->whereBetween('created_at', [$startDate, $endDate]);

        }

        return $query;
    }

}
