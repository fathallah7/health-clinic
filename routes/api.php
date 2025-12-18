<?php

use App\Http\Controllers\Admin\{
    AppointmentController as AdminAppointmentController,
    AvailabilityController,
    TimeSlotController
};
use App\Http\Controllers\User\{
    AppointmentController,
    UserTimeSlotController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'isAdmin'])
    ->group(function () {
        Route::apiResource('availabilities', AvailabilityController::class);
        Route::apiResource('time-slots', TimeSlotController::class);
        Route::apiResource('appointments', AdminAppointmentController::class);
    });

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // User Time SLot
    Route::get('user/time-slots', [UserTimeSlotController::class, 'index']);

    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('time-slots', [AppointmentController::class, 'index']);

require __DIR__ . '/auth.php';
