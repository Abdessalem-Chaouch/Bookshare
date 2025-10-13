<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\AuthorSubscription;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPaymentController extends Controller
{
    public function showPaymentForm(Subscription $subscription)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        return view('BackOffice.payments.form', compact('subscription'));
    }

    public function processPayment(Request $request, Subscription $subscription)
    {
        $request->validate([
            'card_number' => 'required|string|size:19',
            'expiry_date' => 'required|string|size:5',
            'cvv' => 'required|string|size:3',
            'cardholder_name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Créer l'enregistrement de paiement d'abonnement
        $payment = SubscriptionPayment::create([
            'payment_id' => 'SUB_' . Str::random(10),
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $subscription->price,
            'currency' => 'TND',
            'payer_name' => $request->cardholder_name,
            'payer_email' => $user->email,
            'payment_status' => 'pending',
            'payment_method' => 'card'
        ]);

        // Simulation du paiement
        $paymentSuccess = $this->simulatePayment($request->all(), $subscription->price);

        if ($paymentSuccess) {
            // Mettre à jour le statut du paiement
            $payment->update(['payment_status' => 'completed']);

            // Changer le rôle de l'utilisateur en auteur
            $user->update(['role' => 'auteur']);
            
            // Désactiver l'ancien abonnement s'il existe
            $user->authorSubscriptions()->where('is_active', true)->update(['is_active' => false]);

            // Créer le nouvel abonnement
            AuthorSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'starts_at' => now(),
                'expires_at' => now()->addDays($subscription->duration_days),
                'is_active' => true
            ]);

            return redirect()->route('dashboardAuteur')->with('success', 'Paiement réussi ! Vous êtes maintenant auteur.');
        }

        // Mettre à jour le statut du paiement en cas d'échec
        $payment->update(['payment_status' => 'failed']);
        
        return redirect()->back()->with('error', 'Échec du paiement. Veuillez réessayer.');
    }

    public function history()
    {
        $payments = auth()->user()->subscriptionPayments()->with('subscription')->orderBy('created_at', 'desc')->get();
        return view('BackOffice.payments.history', compact('payments'));
    }

    private function simulatePayment($paymentData, $amount)
    {
        if (empty($paymentData['card_number']) || empty($paymentData['cvv'])) {
            return false;
        }

        return rand(1, 100) <= 95;
    }
}