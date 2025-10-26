<?php

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TimeSlotController;
use App\Http\Controllers\User\AppointmentController;
use App\Http\Controllers\User\UserTimeSlotController;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    // Availabilities
    Route::apiResource('/admin/availability', AvailabilityController::class);
    // Time Slots
    Route::apiResource('/admin/time-slot', TimeSlotController::class);
    // Appointments
    Route::apiResource('/admin/appointment', AdminAppointmentController::class);
    // Products
    Route::apiResource('/admin/product', ProductController::class);
    // Categories
    Route::apiResource('/admin/category', CategoryController::class);
});

// User Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Time Slots
    Route::get('/user-time-slot', [UserTimeSlotController::class, 'index']);
    // Appointments
    Route::post('/appointment', [AppointmentController::class, 'store']);
    Route::delete('/appointment/{appointment}', [AppointmentController::class, 'destroy']);
});

// Public Routes
Route::get('/time-slot', [AppointmentController::class, 'index']);



require __DIR__ . '/auth.php';