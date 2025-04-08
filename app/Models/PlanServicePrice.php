<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanServicePrice extends Model
{
    use HasFactory;

    protected $table = 'planServicePrices';

    protected $fillable = [
        'service_id',
        'billing_cycle',
        'price',
        'currency'
    ];

    // Belongs to Relations
    public function planService()
    {
        return $this->belongsTo(PlanService::class, 'service_id', 'id');
    }
}
