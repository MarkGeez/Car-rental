<?php

use App\Models\User;
use App\Models\Vehicle;
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

    $response = $this->get('/cars?search=Toyota');

    $response->assertOk()->assertSee('Toyota')->assertSee('Vios');
    $this->get("/cars/{$vehicle->id}")->assertOk();
});
