<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StolenDeviceController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/search', [StolenDeviceController::class, 'search']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

    // Profile routes
    Route::get('/profile/stats', [ProfileController::class, 'stats']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'changePassword']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    // Dashboard
    Route::get('/dashboard', [StolenDeviceController::class, 'dashboard']);

    // Devices
    Route::apiResource('devices', StolenDeviceController::class);

    // Reports (Security agencies and admins only)
    Route::middleware('role:security_agency,admin')->group(function () {
        Route::get('/reports/devices', [StolenDeviceController::class, 'generateReport']);
    });
});
