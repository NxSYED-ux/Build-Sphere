<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'address';
 
    protected $primaryKey = 'id';
 
    protected $fillable = [
        'location',
        'country',
        'province', 
        'city', 
        'postal_code', 
        'latitude',
        'longitude',
    ];

    protected $casts = [ 
        'latitude' => 'decimal:10,8',
        'longitude' => 'decimal:10,8', 
    ]; 
 
    public $timestamps = true; 
 
}
