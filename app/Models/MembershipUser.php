<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipUser extends Model
{
    use HasFactory;

    protected $table = 'membership_users';

    protected $fillable = [
        'user_id',
        'membership_id',
        'subscription_id',

        'quantity',
        'used',
        'ends_at'
    ];

    protected $casts = [
        'ends_at' => 'date:Y-m-d',
    ];

    // Belongs to relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id', 'id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'id');
    }


}
