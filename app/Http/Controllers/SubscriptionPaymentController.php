<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\AuthorSubscription;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPaymentController extends Controller
{
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

        return response()->json(['success' => true, 'redirect' => route('dashboardAuteur')]);
    }

    public function showPaymentForm(Subscription $subscription)
    {
        return view('BackOffice.payments.form', compact('subscription'));
    }

    public function history()
    {
        $payments = auth()->user()->subscriptionPayments()->with('subscription')->orderBy('created_at', 'desc')->get();
        return view('BackOffice.payments.history', compact('payments'));
    }
}