<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Copier les images vers le storage si elles n'existent pas déjà
        $publicImagesPath = public_path('images');
        $storageImagesPath = storage_path('app/public/categories');
        
        if (!file_exists($storageImagesPath)) {
            mkdir($storageImagesPath, 0755, true);
        }
        
        $imageFiles = [
            'product-item1.jpg',
            'product-item2.jpg', 
            'product-item3.jpg',
            'product-item4.jpg',
            'product-item5.jpg',
            'product-item6.jpg'
        ];
        
        foreach ($imageFiles as $file) {
            $sourcePath = $publicImagesPath . '/' . $file;
            $destPath = $storageImagesPath . '/' . $file;
            
            if (file_exists($sourcePath) && !file_exists($destPath)) {
                copy($sourcePath, $destPath);
            }
        }

        $categories = [
            [
                'name' => 'Fiction',
                'description' => 'Romans et histoires imaginaires',
                'image' => 'categories/product-item1.jpg'
            ],
            [
                'name' => 'Science-Fiction',
                'description' => 'Livres de science-fiction et fantasy',
                'image' => 'categories/product-item2.jpg'
            ],
            [
                'name' => 'Romance',
                'description' => 'Histoires d\'amour et romans romantiques',
                'image' => 'categories/product-item3.jpg'
            ],
            [
                'name' => 'Thriller',
                'description' => 'Suspense et romans policiers',
                'image' => 'categories/product-item4.jpg'
            ],
            [
                'name' => 'Biographie',
                'description' => 'Vies et mémoires de personnalités',
                'image' => 'categories/product-item5.jpg'
            ],
            [
                'name' => 'Histoire',
                'description' => 'Livres d\'histoire et documentaires',
                'image' => 'categories/product-item6.jpg'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}