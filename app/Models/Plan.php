<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'description',
        'currency',
        'status',
        'updated_at'
    ];

    // Has Many Relations
    public function services()
    {
        return $this->hasMany(PlanService::class, 'plan_id', 'id');
    }

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'source');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'source');
    }
}
