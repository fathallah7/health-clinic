<?php

namespace App\Services\Product;

use App\Models\Product;

class ProductService
{
    // Get All Products with Category
    public function getAllProducts()
    {
        return Product::with('category')->get();
    }

    // Get Product By Id
    public function getProductById($id)
    {
        return Product::with('category')->find($id);
    }

    // Create Product
    public function createProduct($data, $image = null)
    {
        if ($image) {
            $data['image'] = $image->store('products', 'public');
        }
        return Product::create($data);
    }

    // Update Product
    public function updateProduct(Product $product, $data, $image = null)
    {
        if ($image) {
            $data['image'] = $image->store('products', 'public');
        }
        $product->update($data);
        return $product;
    }

    // Delete Product
    public function deleteProduct(Product $product)
    {
        $product->delete();
    }
}
