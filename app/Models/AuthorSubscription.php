<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AuthorSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'starts_at',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function isExpired()
    {
        return $this->expires_at < now();
    }

    public function isActive()
    {
        return $this->is_active && !$this->isExpired();
    }
}