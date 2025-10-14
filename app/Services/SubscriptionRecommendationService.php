<?php

namespace App\Services;

use App\Models\User;
use App\Models\Borrow;
use App\Models\Payment;
use Carbon\Carbon;

class SubscriptionRecommendationService
{
    public function getRecommendation(User $user): array
    {
        $empruntsParMois = $this->getMonthlyBorrows($user);
        $budgetMoyen = $this->getAverageBudget($user);
        
        $recommendation = $this->predictSubscription($empruntsParMois, $budgetMoyen);
        
        return [
            'current_usage' => [
                'emprunts_mois' => $empruntsParMois,
                'budget_livres' => $budgetMoyen,
                'abonnement_actuel' => $user->subscription ?? 'Aucun'
            ],
            'recommendation' => $recommendation,
            'message' => $this->getRecommendationMessage($recommendation, $empruntsParMois),
            'action' => $this->getActionMessage($user->subscription ?? 'Aucun', $recommendation)
        ];
    }

    private function getMonthlyBorrows(User $user): int
    {
        // Emprunts de livres (table borrows)
        $borrows = Borrow::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subMonths(3))
            ->count();
            
        return round($borrows / 3);
    }

    private function getAverageBudget(User $user): float
    {
        // Budget basé sur les achats de livres (table payments pour livres)
        $bookPurchases = Payment::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->whereHas('livre') // Seulement les paiements de livres
            ->avg('amount');
            
        return $bookPurchases ?? 0.0; // 0€ si aucun achat
    }

    private function predictSubscription(int $emprunts, float $budget): string
    {
        try {
            $pythonPath = 'python';
            $scriptPath = base_path('ai_model/predict.py');
            $command = "{$pythonPath} {$scriptPath} {$emprunts} {$budget}";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            return $result['prediction'] ?? 'Basic';
        } catch (Exception $e) {
            // Fallback si Python indisponible
            if ($emprunts == 0 && $budget == 0) return 'Basic'; // Nouveau utilisateur
            elseif ($emprunts <= 3 && $budget <= 15) return 'Basic';
            elseif ($emprunts <= 8 && $budget <= 30) return 'Premium';
            else return 'VIP';
        }
    }

    private function getRecommendationMessage(string $recommendation, int $emprunts): string
    {
        switch ($recommendation) {
            case 'Basic':
                return "Basé sur vos {$emprunts} emprunts de livres/mois, l'abonnement Basic (9.99€) vous convient.";
            case 'Premium':
                return "Avec {$emprunts} emprunts de livres/mois, le Premium (19.99€) vous ferait économiser.";
            case 'VIP':
                return "Votre usage intensif de {$emprunts} emprunts de livres/mois justifie le VIP (39.99€).";
            default:
                return "Continuez avec votre abonnement actuel.";
        }
    }
    
    private function getActionMessage(string $current, string $recommended): string
    {
        if ($current == $recommended) {
            return "✅ Votre abonnement est optimal";
        } elseif ($current == 'Aucun') {
            return "🚀 Commencez avec {$recommended}";
        } else {
            return "🔄 Changez pour {$recommended}";
        }
    }
}