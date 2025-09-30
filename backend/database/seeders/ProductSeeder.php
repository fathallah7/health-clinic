<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // / (category_id = 1)
        Product::create([
            'name' => 'Smartphone Pro',
            'description' => 'Latest model with AI camera',
            'price' => 2500.00,
            'stock' => 50,
            'category_id' => 1,
        ]);
        Product::create([
            'name' => 'Wireless Headphones',
            'description' => 'Noise-cancelling feature',
            'price' => 450.50,
            'stock' => 120,
            'category_id' => 1,
        ]);

        /// (category_id = 2)
        Product::create([
            'name' => 'The Laravel Way',
            'description' => 'A deep dive into Laravel framework',
            'price' => 120.00,
            'stock' => 200,
            'category_id' => 2,
        ]);
        Product::create([
            'name' => 'Sci-Fi Novel',
            'description' => 'A journey to another galaxy',
            'price' => 85.75,
            'stock' => 80,
            'category_id' => 2,
        ]);

        // (category_id = 3/ 
        Product::create([
            'name' => 'Cotton T-Shirt',
            'description' => 'Comfortable and stylish',
            'price' => 99.99,
            'stock' => 300,
            'category_id' => 3,
        ]);
    }
}
