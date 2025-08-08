<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StolenDeviceController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/search', [StolenDeviceController::class, 'search']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Dashboard
    Route::get('/dashboard', [StolenDeviceController::class, 'dashboard']);
    
    // Devices
    Route::apiResource('devices', StolenDeviceController::class);
    
    // Reports (Security agencies and admins only)
    Route::middleware('role:security_agency,admin')->group(function () {
        Route::get('/reports/devices', [StolenDeviceController::class, 'generateReport']);
    });
});
