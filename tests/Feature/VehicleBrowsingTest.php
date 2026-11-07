<?php

use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('visitors can browse verified available vehicles', function () {
    $vendorUser = User::factory()->create(['role' => 'vendor']);
    $vendor = Vendor::create([
        'user_id' => $vendorUser->id,
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
        'plate_number' => 'TEST-002',
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1500,
        'security_deposit' => 5000,
        'status' => 'available',
    ]);
    VehicleImage::create([
        'vehicle_id' => $vehicle->id,
        'image_path' => 'vehicle-images/test-car.jpg',
    ]);

    $response = $this->get('/cars?search=Toyota');

    $response->assertOk()->assertSee('Toyota')->assertSee('Vios');
    $this->get("/cars/{$vehicle->id}")
        ->assertOk()
        ->assertSee('storage/vehicle-images/test-car.jpg')
        ->assertSee('Toyota Vios');
});

test('a customer can open the booking form and submit a booking request', function () {
    $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
    $vendorUser = User::factory()->create(['role' => 'vendor']);
    $vendor = Vendor::create([
        'user_id' => $vendorUser->id,
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
        'plate_number' => 'BOOK-001',
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1500,
        'security_deposit' => 5000,
        'status' => 'available',
    ]);
    $pickupDate = now()->addDays(3)->toDateString();
    $returnDate = now()->addDays(5)->toDateString();

    $this->actingAs($customer)
        ->get("/cars/{$vehicle->id}/book")
        ->assertOk()
        ->assertViewIs('user.book');

    $response = $this->actingAs($customer)->post("/cars/{$vehicle->id}/book", [
        'pickup_date' => $pickupDate,
        'return_date' => $returnDate,
    ]);

    $rental = Rental::where('vehicle_id', $vehicle->id)->firstOrFail();
    $response->assertRedirect("/user/rentals/{$rental->id}");
    expect($rental)
        ->customer_id->toBe($customer->id)
        ->rental_days->toBe(3)
        ->total_amount->toBe('4500.00');
});

test('vehicle details show reviews from completed rentals', function () {
    $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
    $vendor = Vendor::create([
        'user_id' => User::factory()->create(['role' => 'vendor'])->id,
        'business_name' => 'Reviewed Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'verification_status' => 'verified',
    ]);
    $vehicle = Vehicle::create([
        'vendor_id' => $vendor->id,
        'brand' => 'Toyota',
        'model' => 'Vios',
        'year' => 2025,
        'plate_number' => 'REVIEW-002',
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
        'pickup_date' => '2026-10-01',
        'return_date' => '2026-10-02',
        'rental_days' => 2,
        'daily_price' => 1500,
        'total_amount' => 3000,
        'commission_amount' => 300,
        'vendor_amount' => 2700,
        'status' => 'completed',
    ]);
    Review::create([
        'rental_id' => $rental->id,
        'rating' => 5,
        'comment' => 'Very clean and comfortable.',
    ]);

    $this->get("/cars/{$vehicle->id}")
        ->assertOk()
        ->assertSee('5/5')
        ->assertSee('Very clean and comfortable.');
});
