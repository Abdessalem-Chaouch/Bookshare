<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = [
            [
                'name' => 'Basic',
                'description' => 'Plan de base pour débuter',
                'price' => 9.99,
                'duration_days' => 30,
                'features' => [
                    'Jusqu\'à 5 emprunts par mois',
                    'Support par email',
                    'Accès aux livres populaires',
                    'Recommandations de base'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'description' => 'Plan avancé pour lecteurs réguliers',
                'price' => 19.99,
                'duration_days' => 30,
                'features' => [
                    'Jusqu\'à 15 emprunts par mois',
                    'Support prioritaire',
                    'Accès anticipé aux nouveautés',
                    'Recommandations personnalisées',
                    'Téléchargement hors ligne'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'VIP',
                'description' => 'Plan premium pour gros lecteurs',
                'price' => 39.99,
                'duration_days' => 30,
                'features' => [
                    'Emprunts illimités',
                    'Support 24/7',
                    'Accès exclusif aux livres rares',
                    'IA de recommandation avancée',
                    'Téléchargement illimité',
                    'Événements exclusifs'
                ],
                'is_active' => true,
            ]
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::updateOrCreate(
                ['name' => $subscription['name']],
                $subscription
            );
        }
    }
}