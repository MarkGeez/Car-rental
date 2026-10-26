<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorRentalController;
use App\Http\Controllers\VendorVehicleController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/vehicles', [VehicleController::class, 'index']);
Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']);

// User routes (role = user)
Route::middleware(['auth:sanctum', 'role:user'])->prefix('user')->group(function () {

    // Account
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Check if a car is free for the chosen dates
    Route::get('/vehicles/{vehicle}/availability', [VehicleController::class, 'availability']);

    // Rentals
    Route::get('/rentals', [RentalController::class, 'index']);
    Route::post('/rentals', [RentalController::class, 'store']);
    Route::get('/rentals/{rental}', [RentalController::class, 'show']);
    Route::patch('/rentals/{rental}/cancel', [RentalController::class, 'cancel']);

    // Simulated payment
    Route::post('/rentals/{rental}/pay', [PaymentController::class, 'pay']);

    // Reviews (only after the rental is completed)
    Route::post('/rentals/{rental}/review', [ReviewController::class, 'store']);
});

// Vendor routes (role = vendor)
Route::middleware(['auth:sanctum', 'role:vendor'])->prefix('vendor')->group(function () {

    // Account
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Business profile (submit business info + permit for verification)
    Route::get('/profile', [VendorController::class, 'show']);
    Route::post('/profile', [VendorController::class, 'store']);
    Route::put('/profile', [VendorController::class, 'update']);

    // Vehicles (index, store, show, update, destroy)
    Route::apiResource('vehicles', VendorVehicleController::class);
    Route::post('/vehicles/{vehicle}/images', [VendorVehicleController::class, 'addImage']);
    Route::delete('/vehicles/{vehicle}/images/{image}', [VendorVehicleController::class, 'deleteImage']);

    // Rentals of my vehicles
    Route::get('/rentals', [VendorRentalController::class, 'index']);
    Route::get('/rentals/{rental}', [VendorRentalController::class, 'show']);
    Route::patch('/rentals/{rental}/approve', [VendorRentalController::class, 'approve']);   // pending  -> approved
    Route::patch('/rentals/{rental}/reject', [VendorRentalController::class, 'reject']);     // pending  -> cancelled
    Route::patch('/rentals/{rental}/start', [VendorRentalController::class, 'start']);       // approved -> ongoing
    Route::patch('/rentals/{rental}/complete', [VendorRentalController::class, 'complete']); // ongoing  -> completed

    // My earnings (sum of vendor_amount)
    Route::get('/earnings', [VendorRentalController::class, 'earnings']);
});

// Admin routes (role = admin)
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {

    // Account
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard (totals + commission earnings)
    Route::get('/dashboard', [AdminController::class, 'dashboard']);

    // Vendor verification
    Route::get('/vendors', [AdminController::class, 'vendors']);              // ?status=pending
    Route::patch('/vendors/{vendor}/verify', [AdminController::class, 'verifyVendor']);
    Route::patch('/vendors/{vendor}/reject', [AdminController::class, 'rejectVendor']);

    // Users
    Route::get('/users', [AdminController::class, 'users']);
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus']); // active | suspended

    // Vehicles and rentals (view only)
    Route::get('/vehicles', [AdminController::class, 'vehicles']);
    Route::get('/rentals', [AdminController::class, 'rentals']);
});
