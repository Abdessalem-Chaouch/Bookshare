<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Livre;
use App\Models\Category;
use App\Models\User;

class LivreSeeder extends Seeder
{
    public function run(): void
    {
        // Create a sample category if none exists
        $category = Category::firstOrCreate(['name' => 'Fiction']);
        
        // Create sample books
        $books = [
            [
                'titre' => 'Le Petit Prince',
                'auteur' => 'Antoine de Saint-Exupéry',
                'description' => 'Un conte philosophique et poétique sous l\'apparence d\'un conte pour enfants.',
                'isbn' => '978-2-07-040850-1',
                'categorie_id' => $category->id,
                'prix' => 15.99,
                'disponibilite' => 'disponible',
                'stock' => 10,
                'date_ajout' => now(),
            ],
            [
                'titre' => 'L\'Étranger',
                'auteur' => 'Albert Camus',
                'description' => 'Roman d\'Albert Camus publié en 1942.',
                'isbn' => '978-2-07-036002-1',
                'categorie_id' => $category->id,
                'prix' => 12.50,
                'disponibilite' => 'disponible',
                'stock' => 5,
                'date_ajout' => now(),
            ],
            [
                'titre' => 'Les Misérables',
                'auteur' => 'Victor Hugo',
                'description' => 'Roman de Victor Hugo paru en 1862.',
                'isbn' => '978-2-07-041108-2',
                'categorie_id' => $category->id,
                'prix' => 18.99,
                'disponibilite' => 'disponible',
                'stock' => 8,
                'date_ajout' => now(),
            ]
        ];

        foreach ($books as $book) {
            Livre::create($book);
        }
    }
}