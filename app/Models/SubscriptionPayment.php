<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'payment_id',
        'user_id',
        'subscription_id',
        'amount',
        'currency',
        'payer_name',
        'payer_email',
        'payment_status',
        'payment_method'
    ];

    protected $casts = [
        'payment_data' => 'array',
       'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    public function isFailed()
    {
        return $this->payment_status === 'failed';
    }
  
}