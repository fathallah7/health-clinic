<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Category\CategoryService;
use App\Traits\ApiResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    public function __construct(protected CategoryService $categoryService) {}

    // Get All Categories
    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return $this->success(CategoryResource::collection($categories), 'Categories List', 200);
    }

    // Create Category
    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());
        return $this->success(new CategoryResource($category), 'Category Created', 200);
    }

    // Update Category
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->categoryService->updateCategory($category, $request->validated());
        return $this->success(new CategoryResource($category), 'Category Updated', 200);
    }

    // Delete Category
    public function destroy(Category $category)
    {
        $this->categoryService->deleteCategory($category);
        return $this->success(null, 'Category Deleted', 200);
    }
}
