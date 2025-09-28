<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        $subscriptions = [
            [
                'name' => 'Basique',
                'description' => 'Abonnement de base pour découvrir LibroLink',
                'price' => 9.99,
                'duration_days' => 30,
                'features' => [
                    'Accès à 100 livres',
                    'Emprunts limités à 3 livres',
                    'Support par email'
                ],
                'is_active' => true
            ],
            [
                'name' => 'Premium',
                'description' => 'Abonnement premium avec plus de fonctionnalités',
                'price' => 19.99,
                'duration_days' => 30,
                'features' => [
                    'Accès illimité aux livres',
                    'Emprunts illimités',
                    'Recommandations personnalisées',
                    'Support prioritaire',
                    'Accès aux nouveautés'
                ],
                'is_active' => true
            ],
            [
                'name' => 'Étudiant',
                'description' => 'Tarif préférentiel pour les étudiants',
                'price' => 4.99,
                'duration_days' => 30,
                'features' => [
                    'Accès à 50 livres académiques',
                    'Emprunts limités à 5 livres',
                    'Ressources éducatives',
                    'Support par email'
                ],
                'is_active' => true
            ]
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}