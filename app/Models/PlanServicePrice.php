<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanServicePrice extends Model
{
    use HasFactory;

    protected $table = 'planserviceprices';

    protected $fillable = [
        'service_id',
        'billing_cycle_id',
        'price'
    ];

    // Belongs to Relations
    public function planService()
    {
        return $this->belongsTo(PlanService::class, 'service_id', 'id');
    }

    public function billingCycle()
    {
        return $this->belongsTo(BillingCycle::class);
    }
}
