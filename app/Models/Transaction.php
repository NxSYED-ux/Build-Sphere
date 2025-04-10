<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaction';

    protected $primaryKey = 'id';

    protected $fillable = [
        'is_admin_transaction',
        'user_id',
        'organization_id',
        'building_id',
        'unit_id',
        'transaction_title',
        'transaction_category',
        'admin_transaction_type',
        'organization_transaction_type',
        'user_transaction_type',
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
        'source_name'
    ];

    protected $casts = [
        'subscription_start_date' => 'datetime',
        'subscription_end_date' => 'datetime',
        'price' => 'decimal:2',
        'is_subscription' => 'boolean',
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
