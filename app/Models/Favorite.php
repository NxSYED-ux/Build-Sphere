<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'favorites';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'unit_id',
    ];

    public $timestamps = true;

    // Belongs to Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function unit()
    {
        return $this->belongsTo(BuildingUnit::class, 'unit_id', 'id');
    }

}
