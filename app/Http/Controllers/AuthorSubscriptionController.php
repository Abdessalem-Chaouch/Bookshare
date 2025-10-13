<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\AuthorSubscription;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuthorSubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::where('is_active', true)->get();
        $currentSubscription = auth()->user()->currentSubscription();
        
        return view('BackOffice.author-subscriptions.index', compact('subscriptions', 'currentSubscription'));
    }

    public function adminIndex()
    {
        $authorSubscriptions = AuthorSubscription::with(['user', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('BackOffice.author-subscriptions.admin-index', compact('authorSubscriptions'));
    }

    public function subscribe(Request $request, Subscription $subscription)
    {
        $user = auth()->user();
        
        if (!$user->isAuteur()) {
            return redirect()->back()->with('error', 'Seuls les auteurs peuvent s\'abonner.');
        }

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

        return redirect()->route('author.subscriptions')->with('success', 'Abonnement activé avec succès!');
    }

    public function transactions()
    {
        $transactions = \App\Models\SubscriptionPayment::with(['user', 'subscription'])
            ->whereHas('user', function($query) {
                $query->where('role', 'auteur');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('BackOffice.author-subscriptions.transactions', compact('transactions'));
    }
}