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
                'name' => 'Basique',
                'description' => 'Plan de base pour débuter',
                'price' => 9.99,
                'duration_days' => 30,
                'features' => [
                    'Publier jusqu\'à 5 livres',
                    'Support par email',
                    'Statistiques de base',
                    'Accès à la communauté'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'description' => 'Plan avancé pour auteurs professionnels',
                'price' => 19.99,
                'duration_days' => 30,
                'features' => [
                    'Livres illimités',
                    'Support prioritaire 24/7',
                    'Statistiques avancées',
                    'Promotion de vos livres',
                    'Badge auteur vérifié',
                    'Accès aux événements exclusifs'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Étudiant',
                'description' => 'Tarif réduit pour étudiants',
                'price' => 4.99,
                'duration_days' => 30,
                'features' => [
                    'Publier jusqu\'à 3 livres',
                    'Support par email',
                    'Accès à la communauté',
                    'Réduction de 50%'
                ],
                'is_active' => true,
            ]
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}