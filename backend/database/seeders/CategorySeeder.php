<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'Electronics', 'description' => 'Gadgets and electronic devices']);
        Category::create(['name' => 'Books', 'description' => 'Fictional and non-fictional books']);
        Category::create(['name' => 'Clothing', 'description' => 'Men and women apparel']);
    }
}
