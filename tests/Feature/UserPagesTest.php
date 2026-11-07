<?php

use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a customer can view their rentals', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'status' => 'active',
    ]);
    $vendor = Vendor::create([
        'user_id' => User::factory()->create(['role' => 'vendor'])->id,
        'business_name' => 'Verified Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'verification_status' => 'verified',
    ]);
    $vehicle = Vehicle::create([
        'vendor_id' => $vendor->id,
        'brand' => 'Toyota',
        'model' => 'Vios',
        'year' => 2025,
        'plate_number' => 'TEST-003',
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1500,
        'security_deposit' => 5000,
        'status' => 'available',
    ]);
    Rental::create([
        'customer_id' => $customer->id,
        'vehicle_id' => $vehicle->id,
        'pickup_date' => '2026-11-10',
        'return_date' => '2026-11-12',
        'rental_days' => 2,
        'daily_price' => 1500,
        'total_amount' => 3000,
        'commission_amount' => 300,
        'vendor_amount' => 2700,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($customer)->get('/user/rentals');

    $response->assertOk()->assertViewIs('user.rentals.index')->assertSee('Toyota')->assertSee('Vios');
});

test('a customer dashboard summarizes their rentals', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'status' => 'active',
    ]);
    $vendor = Vendor::create([
        'user_id' => User::factory()->create(['role' => 'vendor'])->id,
        'business_name' => 'Dashboard Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'verification_status' => 'verified',
    ]);
    $vehicle = Vehicle::create([
        'vendor_id' => $vendor->id,
        'brand' => 'Toyota',
        'model' => 'Vios',
        'year' => 2025,
        'plate_number' => 'DASH-001',
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1500,
        'security_deposit' => 5000,
        'status' => 'available',
    ]);

    foreach ([
        ['status' => 'approved', 'pickup_date' => now()->addDays(2)->toDateString()],
        ['status' => 'completed', 'pickup_date' => now()->subDays(5)->toDateString()],
        ['status' => 'cancelled', 'pickup_date' => now()->addDays(8)->toDateString()],
    ] as $rentalData) {
        Rental::create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'pickup_date' => $rentalData['pickup_date'],
            'return_date' => $rentalData['pickup_date'],
            'rental_days' => 1,
            'daily_price' => 1500,
            'total_amount' => 1500,
            'commission_amount' => 150,
            'vendor_amount' => 1350,
            'status' => $rentalData['status'],
        ]);
    }

    $response = $this->actingAs($customer)->get('/user/dashboard');

    $response
        ->assertOk()
        ->assertViewIs('user.dashboard')
        ->assertViewHas('activeRentals', 1)
        ->assertViewHas('upcomingBookings', 1)
        ->assertViewHas('completedRentals', 1)
        ->assertViewHas('recentRentals', fn ($rentals) => $rentals->count() === 3)
        ->assertSee('Toyota')
        ->assertSee('Vios');
});

test('a customer can view and update their profile', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'status' => 'active',
    ]);

    $this->actingAs($customer)
        ->get('/user/profile')
        ->assertOk()
        ->assertViewIs('user.profile');

    $response = $this->actingAs($customer)->patch('/user/profile', [
        'name' => 'Updated Customer',
        'contact_number' => '09171234567',
    ]);

    $response->assertRedirect(route('user.profile'));
    expect($customer->fresh())
        ->name->toBe('Updated Customer')
        ->contact_number->toBe('09171234567');
});

test('a customer can review a completed rental only once', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'status' => 'active',
    ]);
    $vendor = Vendor::create([
        'user_id' => User::factory()->create(['role' => 'vendor'])->id,
        'business_name' => 'Review Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'verification_status' => 'verified',
    ]);
    $vehicle = Vehicle::create([
        'vendor_id' => $vendor->id,
        'brand' => 'Toyota',
        'model' => 'Vios',
        'year' => 2025,
        'plate_number' => 'REVIEW-001',
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1500,
        'security_deposit' => 5000,
        'status' => 'available',
    ]);
    $rental = Rental::create([
        'customer_id' => $customer->id,
        'vehicle_id' => $vehicle->id,
        'pickup_date' => '2026-11-01',
        'return_date' => '2026-11-02',
        'rental_days' => 2,
        'daily_price' => 1500,
        'total_amount' => 3000,
        'commission_amount' => 300,
        'vendor_amount' => 2700,
        'status' => 'completed',
    ]);

    $this->actingAs($customer)
        ->get("/user/rentals/{$rental->id}")
        ->assertOk()
        ->assertSee('Leave a review');

    $this->actingAs($customer)
        ->get("/user/rentals/{$rental->id}/review")
        ->assertOk()
        ->assertViewIs('user.review');

    $this->actingAs($customer)
        ->post("/user/rentals/{$rental->id}/review", [
            'rating' => 5,
            'comment' => 'Excellent vehicle.',
        ])
        ->assertRedirect(route('user.rentals.show', $rental));

    expect(Review::where('rental_id', $rental->id)->first())
        ->rating->toBe(5)
        ->comment->toBe('Excellent vehicle.');

    $this->actingAs($customer)
        ->get("/user/rentals/{$rental->id}/review")
        ->assertStatus(409);

    $this->actingAs($customer)
        ->get("/user/rentals/{$rental->id}")
        ->assertSee('Review submitted')
        ->assertDontSee('Leave a review');
});

test('a customer cannot view another customers rental', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'status' => 'active',
    ]);
    $otherCustomer = User::factory()->create([
        'role' => 'customer',
        'status' => 'active',
    ]);
    $vendor = Vendor::create([
        'user_id' => User::factory()->create(['role' => 'vendor'])->id,
        'business_name' => 'Verified Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'verification_status' => 'verified',
    ]);
    $vehicle = Vehicle::create([
        'vendor_id' => $vendor->id,
        'brand' => 'Toyota',
        'model' => 'Vios',
        'year' => 2025,
        'plate_number' => 'TEST-004',
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1500,
        'security_deposit' => 5000,
        'status' => 'available',
    ]);
    $rental = Rental::create([
        'customer_id' => $otherCustomer->id,
        'vehicle_id' => $vehicle->id,
        'pickup_date' => '2026-11-10',
        'return_date' => '2026-11-12',
        'rental_days' => 2,
        'daily_price' => 1500,
        'total_amount' => 3000,
        'commission_amount' => 300,
        'vendor_amount' => 2700,
        'status' => 'pending',
    ]);

    $this->actingAs($customer)->get("/user/rentals/{$rental->id}")->assertNotFound();
});
