<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipUsageLog extends Model
{
    use HasFactory;

    protected $table = 'membership_usage_logs';

    protected $fillable = [
        'membership_user_id',
        'usage_date',
        'used',
    ];

    protected $casts = [
        'usage_date' => 'date',
    ];

    // Belongs to relationships
    public function membershipUser()
    {
        return $this->belongsTo(MembershipUser::class);
    }
}
