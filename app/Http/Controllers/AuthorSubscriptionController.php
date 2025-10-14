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
}<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\AuthorSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\SubscriptionMLService;

class AuthorSubscriptionController extends Controller
{
    public function index()
    {
        try {
            $subscriptions = Subscription::where('is_active', true)->get();
            $currentSubscription = auth()->user()->currentSubscription();
            
            return view('BackOffice.author-subscriptions.index', compact('subscriptions', 'currentSubscription'));
        } catch (\Exception $e) {
            // Si erreur, rediriger vers dashboard avec message
            return redirect()->route('dashboardAuteur')
                ->with('error', 'Erreur lors du chargement des abonnements. Veuillez contacter l\'administrateur.');
        }
    }

    public function adminIndex()
    {
        $authorSubscriptions = AuthorSubscription::with(['user', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('BackOffice.author-subscriptions.admin-index', compact('authorSubscriptions'));
    }

    public function destroy($id)
    {
        try {
            $subscription = AuthorSubscription::findOrFail($id);
            $subscription->delete();
            
            return redirect()->route('admin.author-subscriptions')
                ->with('success', 'Abonnement supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('admin.author-subscriptions')
                ->with('error', 'Erreur lors de la suppression de l\'abonnement.');
        }
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

    public function changeSubscription()
    {
        $user = auth()->user();
        
        if (!$user->isAuteur()) {
            return redirect()->back()->with('error', 'Seuls les auteurs peuvent gérer leurs abonnements.');
        }

        // Désactiver l'abonnement actuel pour permettre le changement
        $user->authorSubscriptions()->where('is_active', true)->update(['is_active' => false]);

        return redirect()->route('author.subscriptions')->with('success', 'Abonnement désactivé avec succès! Vous pouvez maintenant choisir un nouveau plan.');
    }

    public function unsubscribe()
    {
        $user = auth()->user();
        
        if (!$user->isAuteur()) {
            return redirect()->back()->with('error', 'Seuls les auteurs peuvent gérer leurs abonnements.');
        }

        // Désactiver l'abonnement actuel (désabonnement complet)
        $user->authorSubscriptions()->where('is_active', true)->update(['is_active' => false]);

        return redirect()->route('author.subscriptions')->with('success', 'Vous avez été désabonné avec succès.');
    }

    public function refreshStats()
    {
        try {
            // Vider tous les caches
            Cache::flush();
            \DB::connection()->getPdo()->exec('SET SESSION query_cache_type = OFF');
            \DB::flushQueryLog();
            \DB::reconnect();
            
            return response()->json([
                'success' => true,
                'message' => 'Statistiques actualisées avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'actualisation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function aiAnalysis()
    {
        try {
            Log::info('Starting ML Analysis for subscriptions');
            
            // Utiliser le service ML
            $mlService = new SubscriptionMLService();
            $mlResults = $mlService->performMLAnalysis();
            
            // Statistiques de base pour compatibilité
            $stats = $this->getSubscriptionStats();
            
            Log::info('ML Analysis completed successfully', [
                'confidence' => $mlResults['confidence'],
                'predictions_count' => count($mlResults['predictions'])
            ]);
            
            return response()->json([
                'success' => true,
                'analysis' => $mlResults['analysis'],
                'stats' => $stats,
                'ml_predictions' => $mlResults['predictions'],
                'confidence' => $mlResults['confidence'],
                'recommendations' => $mlResults['recommendations'],
                'source' => 'machine_learning',
                'algorithm' => 'regression_clustering_prediction'
            ]);
            
        } catch (\Exception $e) {
            Log::error('ML Analysis Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse ML: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getSubscriptionStats()
    {
        // Forcer le rechargement des données sans cache
        \DB::connection()->getPdo()->exec('SET SESSION query_cache_type = OFF');
        \Cache::flush();
        
        $totalSubscriptions = AuthorSubscription::withoutGlobalScopes()->count();
        $activeSubscriptions = AuthorSubscription::withoutGlobalScopes()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->count();
        $expiredSubscriptions = AuthorSubscription::withoutGlobalScopes()
            ->where('expires_at', '<', now())
            ->orWhere('is_active', false)
            ->count();
        $thisMonthSubscriptions = AuthorSubscription::withoutGlobalScopes()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $popularSubscription = AuthorSubscription::withoutGlobalScopes()
            ->with('subscription')
            ->selectRaw('subscription_id, COUNT(*) as count')
            ->groupBy('subscription_id')
            ->orderBy('count', 'desc')
            ->first();

        return [
            'total' => $totalSubscriptions,
            'active' => $activeSubscriptions,
            'expired' => $expiredSubscriptions,
            'this_month' => $thisMonthSubscriptions,
            'popular_plan' => $popularSubscription->subscription->name ?? 'Aucun',
            'conversion_rate' => $totalSubscriptions > 0 ? round(($activeSubscriptions / $totalSubscriptions) * 100, 2) : 0
        ];
    }

    private function generateAnalysisPrompt($stats)
    {
        return "Analysez ces statistiques d'abonnements d'auteurs: Total: {$stats['total']}, Actifs: {$stats['active']}, Expirés: {$stats['expired']}, Ce mois: {$stats['this_month']}, Plan populaire: {$stats['popular_plan']}, Taux de conversion: {$stats['conversion_rate']}%. Donnez un résumé concis avec des recommandations.";
    }
    
    private function generateSmartAnalysis($stats)
    {
        $analysis = "🤖 Analyse IA des Abonnements d'Auteurs\n\n";
        
        // Analyse du taux de conversion avec IA
        if ($stats['conversion_rate'] > 80) {
            $analysis .= "🎆 Performance exceptionnelle! Votre taux de conversion de {$stats['conversion_rate']}% dépasse largement la moyenne du secteur (60-70%). Vos offres d'abonnement sont parfaitement alignées avec les besoins des auteurs.\n\n";
        } elseif ($stats['conversion_rate'] > 60) {
            $analysis .= "📈 Bonne performance avec {$stats['conversion_rate']}% de conversion. Vous êtes dans la moyenne du secteur. Optimisation possible en analysant les points de friction dans le parcours d'abonnement.\n\n";
        } else {
            $analysis .= "⚠️ Attention: Taux de conversion de {$stats['conversion_rate']}% en dessous des standards (60%+). Analyse urgente nécessaire des barrières à l'abonnement.\n\n";
        }
        
        // Analyse de la croissance avec prédictions
        $growth_rate = $stats['total'] > 0 ? ($stats['this_month'] / $stats['total']) * 100 : 0;
        if ($stats['this_month'] > 0) {
            $analysis .= "📈 Dynamique positive: {$stats['this_month']} nouveaux abonnements ce mois (" . round($growth_rate, 1) . "% de croissance). ";
            if ($growth_rate > 20) {
                $analysis .= "Croissance exceptionnelle! Maintenez cette dynamique.\n";
            } elseif ($growth_rate > 10) {
                $analysis .= "Croissance solide et durable.\n";
            } else {
                $analysis .= "Croissance modérée, potentiel d'accélération.\n";
            }
        } else {
            $analysis .= "🚨 Stagnation: Aucun nouvel abonnement ce mois. Action immédiate requise!\n";
        }
        
        // Analyse de la rétention
        $retention_rate = $stats['total'] > 0 ? (($stats['active'] / $stats['total']) * 100) : 0;
        $analysis .= "\n🔄 Taux de rétention: " . round($retention_rate, 1) . "%. ";
        if ($retention_rate > 80) {
            $analysis .= "Excellente fidélisation des auteurs!\n";
        } elseif ($retention_rate > 60) {
            $analysis .= "Rétention correcte, amélioration possible.\n";
        } else {
            $analysis .= "Problème de rétention critique à résoudre.\n";
        }
        
        // Recommandations IA personnalisées
        $analysis .= "\n🎯 Recommandations IA Personnalisées:\n";
        
        if ($stats['expired'] > $stats['active'] * 0.3) {
            $analysis .= "• 🔔 Urgence: Implémentez un système de notifications automatiques 7 jours avant expiration\n";
        }
        
        if ($stats['popular_plan'] !== 'Aucun') {
            $analysis .= "• 🎆 Capitalisez sur le succès du plan '{$stats['popular_plan']}': créez des variantes premium\n";
        }
        
        if ($growth_rate < 5) {
            $analysis .= "• 📊 Lancez une campagne d'acquisition ciblée sur les auteurs indépendants\n";
        }
        
        if ($stats['conversion_rate'] < 60) {
            $analysis .= "• 🔍 Auditez le tunnel d'abonnement: simplifiez le processus de paiement\n";
        }
        
        $analysis .= "• 📊 Analysez les retours utilisateurs pour identifier les fonctionnalités les plus demandées";
        
        return $analysis;
    }
}