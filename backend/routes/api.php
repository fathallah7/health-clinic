<?php

use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::middleware(['auth:sanctum' , 'isAdmin'])->group(function () {
    
    // Availabilities
    Route::apiResource('admin/availability', AvailabilityController::class);

    // Products
    Route::apiResource('admin/product', ProductController::class);
});

require __DIR__.'/auth.php';