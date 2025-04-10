<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingCycle extends Model
{
    use HasFactory;

    protected $table = 'billing_cycles';

    protected $fillable = [
        'duration_months',
        'description'
    ];

    public function planServicePrices()
    {
        return $this->hasMany(PlanServicePrice::class);
    }
}

