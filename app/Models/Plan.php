<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Has Many Relations
    public function services()
    {
        return $this->hasMany(PlanService::class, 'plan_id', 'id');
    }

    public function subscriptions()
    {
        return $this->hasMany(PlanSubscription::class, 'plan_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(PlanPayment::class, 'plan_id', 'id');
    }
}
