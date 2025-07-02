<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPropertyInteraction extends Model
{
    use HasFactory;

    protected $table = 'user_property_interactions';

    protected $fillable = [
        'user_id',
        'unit_id',
        'interaction_type',
        'timestamp',
    ];

    public $timestamps = false;

    // Belongs to Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(BuildingUnit::class, 'unit_id');
    }
}
