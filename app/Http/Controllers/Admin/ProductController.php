<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Product\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(protected ProductService $productService) {}

    // Get All Products with Category
    public function index()
    {
        $products = $this->productService->getAllProducts();
        return $this->success(ProductResource::collection($products), 'Products List', 200);
    }

    public function store(ProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated(), $request->file('image'));
        return $this->success(new ProductResource($product), 'Product Created', 200);
    }

    // Show Product
    public function show(Product $product)
    {
        return $this->success(new ProductResource($product), 'Product Details', 200);
    }

    // Update Product
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product = $this->productService->updateProduct($product, $request->validated(), $request->file('image'));
        return $this->success(new ProductResource($product), 'Product Updated', 200);
    }

    // Delete Product
    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product);
        return $this->success(null, 'Product Deleted', 200);
    }
}
