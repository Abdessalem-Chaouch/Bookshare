<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription_Payment extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'payment_data'
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

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';

    }
  
}