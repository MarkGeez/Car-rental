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

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/cars', [VehicleController::class, 'index'])->name('cars.index');
Route::get('/cars/{vehicle}', [VehicleController::class, 'show'])->name('cars.show');

Route::middleware(['auth', 'role:customer'])->prefix('user')->group(function () {
    Route::get('/dashboard', fn () => view('user.dashboard'))->name('user.dashboard');
    Route::get('/me', [AuthController::class, 'me'])->name('user.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');
    Route::get('/vehicles/{vehicle}/availability', [VehicleController::class, 'availability']);
    Route::get('/rentals', [RentalController::class, 'index']);
    Route::post('/rentals', [RentalController::class, 'store']);
    Route::get('/rentals/{rental}', [RentalController::class, 'show']);
    Route::patch('/rentals/{rental}/cancel', [RentalController::class, 'cancel']);
    Route::post('/rentals/{rental}/pay', [PaymentController::class, 'pay']);
    Route::post('/rentals/{rental}/review', [ReviewController::class, 'store']);
});

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->group(function () {
    Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
    Route::get('/me', [AuthController::class, 'me'])->name('vendor.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('vendor.logout');
    Route::get('/profile', [VendorController::class, 'show']);
    Route::post('/profile', [VendorController::class, 'store']);
    Route::match(['PUT', 'PATCH'], '/profile', [VendorController::class, 'update']);
    Route::resource('vehicles', VendorVehicleController::class);
    Route::post('/vehicles/{vehicle}/images', [VendorVehicleController::class, 'addImage']);
    Route::delete('/vehicles/{vehicle}/images/{image}', [VendorVehicleController::class, 'deleteImage']);
    Route::get('/rentals', [VendorRentalController::class, 'index']);
    Route::get('/rentals/{rental}', [VendorRentalController::class, 'show']);
    Route::patch('/rentals/{rental}/approve', [VendorRentalController::class, 'approve']);
    Route::patch('/rentals/{rental}/reject', [VendorRentalController::class, 'reject']);
    Route::patch('/rentals/{rental}/start', [VendorRentalController::class, 'start']);
    Route::patch('/rentals/{rental}/complete', [VendorRentalController::class, 'complete']);
    Route::get('/earnings', [VendorRentalController::class, 'earnings']);
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/me', [AuthController::class, 'me'])->name('admin.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('/vendors', [AdminController::class, 'vendors']);
    Route::get('/vendors/{vendor}', [AdminController::class, 'showVendor']);
    Route::patch('/vendors/{vendor}/verify', [AdminController::class, 'verifyVendor']);
    Route::patch('/vendors/{vendor}/reject', [AdminController::class, 'rejectVendor']);
    Route::get('/users', [AdminController::class, 'users']);
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus']);
    Route::get('/vehicles', [AdminController::class, 'vehicles']);
    Route::get('/rentals', [AdminController::class, 'rentals']);
    Route::get('/rentals/{rental}', [AdminController::class, 'showRental']);
});
