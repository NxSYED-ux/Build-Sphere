<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserUnitPicture extends Model
{
    use HasFactory;

    protected $table = 'userUnitPictures';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_unit_id',
        'file_path',
        'file_name',
    ];

    public $timestamps = true;

    // Belongs to Relations
    public function userUnit()
    {
        return $this->belongsTo(UserBuildingUnit::class, 'user_unit_id', 'id');
    }
}
