<?php

namespace App\Services;

use App\Models\AuthorSubscription;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SubscriptionMLService
{
    public function performMLAnalysis()
    {
        Cache::flush();
        
        $data = $this->collectRealData();
        $predictions = $this->runAnalysis($data);
        $insights = $this->generateInsights($data, $predictions);
        
        return [
            'analysis' => $insights,
            'predictions' => $predictions,
            'confidence' => $this->calculateConfidence($data),
            'recommendations' => $this->generateRecommendations($data, $predictions)
        ];
    }
    
    private function collectRealData()
    {
        $total = AuthorSubscription::count();
        $active = AuthorSubscription::where('is_active', true)
                                  ->where('expires_at', '>', now())
                                  ->count();
        $expired = $total - $active;
        
        $thisMonth = AuthorSubscription::whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->count();
        
        $lastMonth = AuthorSubscription::whereMonth('created_at', now()->subMonth()->month)
                                     ->whereYear('created_at', now()->subMonth()->year)
                                     ->count();
        
        $plans = AuthorSubscription::select('subscription_id', DB::raw('COUNT(*) as count'))
                                 ->with('subscription')
                                 ->groupBy('subscription_id')
                                 ->get();
        
        return [
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'plans' => $plans
        ];
    }
    
    private function runAnalysis($data)
    {
        // Calcul correct du taux de désabonnement
        $churnRate = $data['total'] > 0 ? ($data['expired'] / $data['total']) * 100 : 0;
        
        // Calcul correct du taux de croissance
        $growthRate = $data['last_month'] > 0 ? 
            (($data['this_month'] - $data['last_month']) / $data['last_month']) * 100 : 
            ($data['this_month'] > 0 ? 100 : 0);
        
        // Prédiction basée sur la tendance actuelle
        $predictedChurn = max(0, min(100, $churnRate + ($growthRate * -0.05)));
        $predictedGrowth = max(0, round($data['this_month'] * (1 + ($growthRate / 100))));
        
        return [
            'churn_prediction' => [
                'current_churn_rate' => round($churnRate, 1),
                'predicted_churn_rate' => round($predictedChurn, 1),
                'risk_level' => $this->getRiskLevel($churnRate)
            ],
            'growth_forecast' => [
                'current_month' => $data['this_month'],
                'last_month' => $data['last_month'],
                'growth_rate' => round($growthRate, 1),
                'next_month' => $predictedGrowth,
                'total_periods' => 2
            ]
        ];
    }
    
    private function generateInsights($data, $predictions)
    {
        $insights = "🤖 ANALYSE IA PRÉDICTIVE DES ABONNEMENTS\n\n";
        
        // Analyse de rétention avec données réelles
        $churn = $predictions['churn_prediction'];
        $retentionRate = 100 - $churn['current_churn_rate'];
        
        $insights .= "📊 ANALYSE DE RÉTENTION CLIENT\n";
        $insights .= "• Abonnements totaux : {$data['total']}\n";
        $insights .= "• Abonnements actifs : {$data['active']}\n";
        $insights .= "• Taux de rétention : " . round($retentionRate, 1) . "%\n";
        $insights .= "• Niveau de risque : {$churn['risk_level']}\n";
        $insights .= "• Prévision 30 jours : {$churn['predicted_churn_rate']}% de désabonnement\n\n";
        
        // Analyse de croissance avec données réelles
        $growth = $predictions['growth_forecast'];
        $insights .= "📈 PROJECTION DE CROISSANCE\n";
        $insights .= "• Ce mois : {$growth['current_month']} nouveaux abonnements\n";
        $insights .= "• Mois précédent : {$growth['last_month']} abonnements\n";
        $insights .= "• Évolution : {$growth['growth_rate']}%\n";
        $insights .= "• Tendance : " . $this->getTrendEmoji($growth['growth_rate']) . "\n";
        $insights .= "• Prévision mois prochain : {$growth['next_month']} abonnements\n\n";
        
        // Performance des plans avec données réelles
        if ($data['plans']->count() > 0) {
            $topPlan = $data['plans']->sortByDesc('count')->first();
            $planPerformance = $data['total'] > 0 ? round(($topPlan->count / $data['total']) * 100, 1) : 0;
            
            $insights .= "🎯 PERFORMANCE DES OFFRES\n";
            $insights .= "• Plan le plus populaire : {$topPlan->subscription->name}\n";
            $insights .= "• Part de marché : {$planPerformance}%\n";
            $insights .= "• Nombre d'abonnés : {$topPlan->count}\n";
        } else {
            $insights .= "🎯 PERFORMANCE DES OFFRES\n";
            $insights .= "• Aucune donnée d'abonnement disponible\n";
        }
        
        return $insights;
    }
    
    private function generateRecommendations($data, $predictions)
    {
        return [];
    }
    
    private function getTrendEmoji($growthRate)
    {
        if ($growthRate > 10) return "🚀 Forte croissance";
        if ($growthRate > 0) return "📈 Croissance positive";
        if ($growthRate == 0) return "📊 Stabilité";
        if ($growthRate > -10) return "📉 Léger déclin";
        return "🔻 Déclin important";
    }
    
    private function getRiskLevel($churnRate)
    {
        if ($churnRate > 30) return 'CRITIQUE';
        if ($churnRate > 20) return 'ÉLEVÉ';
        if ($churnRate > 10) return 'MODÉRÉ';
        return 'FAIBLE';
    }
    
    private function calculateConfidence($data)
    {
        if ($data['total'] >= 20) return 'TRÈS ÉLEVÉE';
        if ($data['total'] >= 10) return 'ÉLEVÉE';
        if ($data['total'] >= 5) return 'MOYENNE';
        return 'FAIBLE';
    }
}