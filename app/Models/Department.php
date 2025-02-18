<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Department extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
    */
    protected $table = 'departments';

    /**
     * The primary key associated with the table.
     *
     * @var string
    */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */
    protected $fillable = [
        'name',
        'description',
        'organization_id', 
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
    */
    public $timestamps = true;

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Boot method for setting created_by and updated_by automatically.
    */
    protected static function boot()
    {
        parent::boot();
 
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });
 
        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }
}
