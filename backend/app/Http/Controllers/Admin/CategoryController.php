<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    // Get All Categories
    public function index()
    {
        $categories = Category::all();
        return $this->success($categories, 'Categories List', 200);
    }

    // Create Category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:255|unique:categories,name',
            'description' => 'max:255',
        ]);
        $category = Category::create($request->all());
        return $this->success($category, 'Category Created', 200);
    }

    // Update Category
    public function update(Request $request, Category $category)
    {
        $request->validate([
            "name" => "string|required|min:3|max:255|unique:categories,name,{$category->id}",
            'description' => 'max:255',
        ]);
        $category->update($request->all());
        return $this->success($category, 'Category Updated', 200);
    }

    // Delete Category
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->success(null, 'Category Deleted', 200);
    }
}
