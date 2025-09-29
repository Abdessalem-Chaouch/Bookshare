<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'livre_id',
        'user_id',
        'product_name',
        'amount',
        'currency',
        'payer_name',
        'payer_email',
        'payment_status',
        'payment_method',
    ];



     public function livre()
    {
        return $this->belongsTo(Livre::class, 'livre_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');

/*use Illuminate\Database\Eloquent\Model;

class Payment extends Model
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
  */
}
