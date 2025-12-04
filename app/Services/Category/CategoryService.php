<?php

namespace App\Services\Category;

use App\Models\Category;

class CategoryService
{
    // Get all categories
    public function getAllCategories()
    {
        return Category::all();
    }

    // Create category
    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    // Update category
    public function updateCategory(Category $category, array $data)
    {
        $category->update($data);
        return $category->fresh();
    }

    // Delete category
    public function deleteCategory(Category $category)
    {
        $category->delete();
    }
}
