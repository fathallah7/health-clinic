<?php

use App\Http\Controllers\Admin\AvailabilityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::middleware(['auth:sanctum' , 'isAdmin'])->group(function () {
    Route::apiResource('admin/availability', AvailabilityController::class);
});

require __DIR__.'/auth.php';