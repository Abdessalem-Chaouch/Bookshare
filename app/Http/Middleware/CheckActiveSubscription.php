<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckActiveSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Si l'utilisateur est admin, il peut tout faire
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        // Si l'utilisateur est auteur, vérifier l'abonnement
        if ($user->isAuteur() && !$user->hasActiveSubscription()) {
            return redirect()->route('dashboardAuteur')
                ->with('error', 'Vous devez avoir un abonnement actif pour ajouter des livres. Veuillez vous abonner à un plan.');
        }
        
        return $next($request);
    }
}