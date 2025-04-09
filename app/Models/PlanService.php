<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanService extends Model
{
    use HasFactory;

    protected $table = 'planServices';

    protected $fillable = [
        'plan_id',
        'name',
        'keyword',
        'quantity',
        'status',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // Belongs to Relations
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    // Has Many Relations
    public function subscriptionItems()
    {
        return $this->hasMany(PlanSubscriptionItem::class, 'service_id', 'id');
    }

    public function prices()
    {
        return $this->hasMany(PlanServicePrice::class, 'service_id', 'id');
    }

}
