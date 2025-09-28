<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Fiction',
                'description' => 'Romans et histoires imaginaires',
                'image' => 'images/product-item1.jpg'
            ],
            [
                'name' => 'Science-Fiction',
                'description' => 'Livres de science-fiction et fantasy',
                'image' => 'images/product-item2.jpg'
            ],
            [
                'name' => 'Romance',
                'description' => 'Histoires d\'amour et romans romantiques',
                'image' => 'images/product-item3.jpg'
            ],
            [
                'name' => 'Thriller',
                'description' => 'Suspense et romans policiers',
                'image' => 'images/product-item4.jpg'
            ],
            [
                'name' => 'Biographie',
                'description' => 'Vies et mémoires de personnalités',
                'image' => 'images/product-item5.jpg'
            ],
            [
                'name' => 'Histoire',
                'description' => 'Livres d\'histoire et documentaires',
                'image' => 'images/product-item6.jpg'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}