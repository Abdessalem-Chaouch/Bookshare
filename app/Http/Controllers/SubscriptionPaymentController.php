<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\AuthorSubscription;
use App\Models\SubscriptionPayment;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPaymentController extends Controller
{
    protected $invoiceService;
    protected $currencyService;

    public function __construct(InvoiceService $invoiceService, CurrencyService $currencyService)
    {
        $this->invoiceService = $invoiceService;
        $this->currencyService = $currencyService;
    }
    public function processPayment(Request $request, Subscription $subscription)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $payment = SubscriptionPayment::create([
            'payment_id' => 'SUB_' . Str::random(10),
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $subscription->price,
            'currency' => 'USD',
            'payer_name' => $user->name,
            'payer_email' => $user->email,
            'payment_status' => 'completed',
            'payment_method' => 'api'
        ]);

        // Simulation de paiement réussi
        $user->update(['role' => 'auteur']);
        
        $user->authorSubscriptions()->where('is_active', true)->update(['is_active' => false]);

        AuthorSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'starts_at' => now(),
            'expires_at' => now()->addDays($subscription->duration_days),
            'is_active' => true
        ]);

        // Generate invoice automatically
        $invoice = $this->invoiceService->generateInvoice($payment);

        return response()->json([
            'success' => true, 
            'redirect' => route('author.subscriptions'),
            'invoice_id' => $invoice->id
        ]);
    }

    public function showPaymentForm(Subscription $subscription)
    {
        $userCurrency = $this->currencyService->getUserCurrency();
        
        // Convert price to user's currency
        $subscription->display_price = $this->currencyService->getLocalizedPrice(
            $subscription->price,
            'USD',
            $userCurrency
        );
        $subscription->display_currency = $userCurrency;
        
        return view('BackOffice.payments.form', compact('subscription'));
    }

    public function history()
    {
        $payments = auth()->user()->subscriptionPayments()->with(['subscription', 'invoice'])->orderBy('created_at', 'desc')->get();
        return view('BackOffice.payments.history', compact('payments'));
    }

    public function downloadInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        // Check if user owns this invoice
        if ($invoice->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to invoice');
        }

        return $this->invoiceService->downloadInvoice($invoice);
    }

    public function viewInvoice($id)
    {
        $invoice = Invoice::with(['user', 'subscription', 'subscriptionPayment'])->findOrFail($id);
        
        // Check if user owns this invoice
        if ($invoice->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to invoice');
        }

        return $this->invoiceService->streamInvoice($invoice);
    }
}