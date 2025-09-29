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
    }
}
