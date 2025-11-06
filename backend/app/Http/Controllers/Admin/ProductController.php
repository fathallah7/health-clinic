<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    // Get All Products with Category
    public function index()
    {
        $products = Product::with('category')->get();
        return $this->success($products, 'Products List', 200);
    }

    public function store(ProductRequest $request)
    {
        $validated = $request->validated();
        $product = Product::create($validated);
        return $this->success($product, 'Product Created', 200);
    }

    // Show Product
    public function show(Product $product)
    {
        return $this->success($product, 'Product Details', 200);
    }

    // Update Product
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $validated = $request->validated();
        $product->update($validated);
        return $this->success($product, 'Product Updated', 200);
    }

    // Delete Product
    public function destroy(Product $product)
    {
        $product->delete();
        return $this->success(null, 'Product Deleted', 200);
    }
}
