<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'subscription_payment_id',
        'subscription_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'currency',
        'invoice_date',
        'due_date',
        'status',
        'notes',
        'pdf_path'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPayment()
    {
        return $this->belongsTo(SubscriptionPayment::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    // Generate unique invoice number
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return sprintf('INV-%s%s-%04d', $year, $month, $number);
    }

    // Get formatted invoice number for display
    public function getFormattedNumberAttribute()
    {
        return $this->invoice_number;
    }

    // Check if invoice is paid
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    // Check if invoice is overdue
    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date < now();
    }
}
