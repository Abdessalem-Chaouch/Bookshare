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
        $insights = "🤖 AI PREDICTIVE SUBSCRIPTION ANALYSIS\n\n";
        
        // Analyse de rétention avec données réelles
        $churn = $predictions['churn_prediction'];
        $retentionRate = 100 - $churn['current_churn_rate'];
        
        $insights .= "📊 CLIENT RETENTION ANALYSIS\n";
        $insights .= "• Total subscriptions: {$data['total']}\n";
        $insights .= "• Active subscriptions: {$data['active']}\n";
        $insights .= "• Retention rate: " . round($retentionRate, 1) . "%\n";
        $insights .= "• Risk level: {$churn['risk_level']}\n";
        $insights .= "• 30-day forecast: {$churn['predicted_churn_rate']}% churn\n\n";
        
        // Analyse de croissance avec données réelles
        $growth = $predictions['growth_forecast'];
        $insights .= "📈 GROWTH PROJECTION\n";
        $insights .= "• This month: {$growth['current_month']} new subscriptions\n";
        $insights .= "• Previous month: {$growth['last_month']} subscriptions\n";
        $insights .= "• Evolution: {$growth['growth_rate']}%\n";
        $insights .= "• Trend: " . $this->getTrendEmoji($growth['growth_rate']) . "\n";
        $insights .= "• Next month forecast: {$growth['next_month']} subscriptions\n\n";
        
        // Performance des plans avec données réelles
        if ($data['plans']->count() > 0) {
            $topPlan = $data['plans']->sortByDesc('count')->first();
            $planPerformance = $data['total'] > 0 ? round(($topPlan->count / $data['total']) * 100, 1) : 0;
            
            $insights .= "🎯 OFFER PERFORMANCE\n";
            $insights .= "• Most popular plan: {$topPlan->subscription->name}\n";
            $insights .= "• Market share: {$planPerformance}%\n";
            $insights .= "• Number of subscribers: {$topPlan->count}\n";
        } else {
            $insights .= "🎯 OFFER PERFORMANCE\n";
            $insights .= "• No subscription data available\n";
        }
        
        return $insights;
    }
    
    private function generateRecommendations($data, $predictions)
    {
        return [];
    }
    
    private function getTrendEmoji($growthRate)
    {
        if ($growthRate > 10) return "🚀 Strong growth";
        if ($growthRate > 0) return "📈 Positive growth";
        if ($growthRate == 0) return "📊 Stability";
        if ($growthRate > -10) return "📉 Slight decline";
        return "🔻 Significant decline";
    }
    
    private function getRiskLevel($churnRate)
    {
        if ($churnRate > 30) return 'CRITICAL';
        if ($churnRate > 20) return 'HIGH';
        if ($churnRate > 10) return 'MODERATE';
        return 'LOW';
    }
    
    private function calculateConfidence($data)
    {
        if ($data['total'] >= 20) return 'VERY HIGH';
        if ($data['total'] >= 10) return 'HIGH';
        if ($data['total'] >= 5) return 'MEDIUM';
        return 'LOW';
    }
}