<?php

use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function vendorUser(string $verificationStatus = 'pending'): array
{
    $user = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
    $vendor = Vendor::create([
        'user_id' => $user->id,
        'business_name' => 'Vendor Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'verification_status' => $verificationStatus,
    ]);

    return [$user, $vendor];
}

function vehicleForVendor(Vendor $vendor, string $plate = 'VAN-001'): Vehicle
{
    return $vendor->vehicles()->create([
        'brand' => 'Toyota',
        'model' => 'Vios',
        'year' => 2025,
        'plate_number' => $plate,
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1500,
        'security_deposit' => 5000,
        'status' => 'available',
    ]);
}

test('the vendor me route redirects to the vendor profile route', function () {
    [$user] = vendorUser('verified');

    $this->actingAs($user)
        ->get('/vendor/me')
        ->assertRedirect(route('vendor.profile'));
});

test('a vendor can submit a business profile and remains blocked until verified', function () {
    Storage::fake('public');
    $user = User::factory()->create(['role' => 'vendor', 'status' => 'active']);

    $response = $this->actingAs($user)->post('/vendor/profile', [
        'business_name' => 'New Fleet',
        'description' => 'Reliable cars',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'business_permit' => UploadedFile::fake()->image('permit.jpg'),
    ]);

    $response->assertRedirect(route('vendor.me'));
    expect($user->fresh()->vendor->verification_status)->toBe('pending');
    $this->actingAs($user)->get('/vendor/vehicles/create')->assertForbidden();
});

test('a vendor can submit a png business permit image', function () {
    Storage::fake('public');
    $user = User::factory()->create(['role' => 'vendor', 'status' => 'active']);

    $response = $this->actingAs($user)->post('/vendor/profile', [
        'business_name' => 'PNG Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'business_permit' => UploadedFile::fake()->image('permit.png'),
    ]);

    $response->assertRedirect(route('vendor.me'));
    expect($user->fresh()->vendor->business_permit)->not->toBeNull();
});

test('a vendor cannot submit a pdf business permit', function () {
    $user = User::factory()->create(['role' => 'vendor', 'status' => 'active']);

    $response = $this->actingAs($user)->post('/vendor/profile', [
        'business_name' => 'PDF Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'business_permit' => UploadedFile::fake()->create('permit.pdf', 100, 'application/pdf'),
    ]);

    $response->assertSessionHasErrors('business_permit');
    expect($user->fresh()->vendor)->toBeNull();
});

test('a verified vendor can create and delete vehicles with images', function () {
    Storage::fake('public');
    [$user, $vendor] = vendorUser('verified');

    $response = $this->actingAs($user)->post('/vendor/vehicles', [
        'brand' => 'Toyota',
        'model' => 'Vios',
        'year' => 2025,
        'plate_number' => 'VAN-002',
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1500,
        'security_deposit' => 5000,
        'available' => '1',
        'images' => [UploadedFile::fake()->image('car.jpg')],
    ]);

    $vehicle = Vehicle::where('plate_number', 'VAN-002')->firstOrFail();
    $response->assertRedirect(route('vehicles.index'));
    expect($vehicle->images)->toHaveCount(1);

    $this->actingAs($user)->delete("/vendor/vehicles/{$vehicle->id}")->assertRedirect(route('vehicles.index'));
    expect(Vehicle::find($vehicle->id))->toBeNull();
});

test('a verified vendor can edit a vehicle and change its status', function () {
    [$user, $vendor] = vendorUser('verified');
    $vehicle = vehicleForVendor($vendor, 'VAN-004');

    $this->actingAs($user)
        ->get('/vendor/vehicles')
        ->assertOk()
        ->assertSee('Edit listing')
        ->assertSee('Unavailable');

    $this->actingAs($user)
        ->patch("/vendor/vehicles/{$vehicle->id}", [
            'brand' => 'Honda',
            'model' => 'City',
            'year' => 2024,
            'plate_number' => 'VAN-004',
            'vehicle_type' => 'Sedan',
            'transmission' => 'Automatic',
            'fuel_type' => 'Gasoline',
            'seats' => 5,
            'daily_price' => 1800,
            'security_deposit' => 6000,
            'description' => 'Updated listing',
            'status' => 'available',
        ])
        ->assertRedirect(route('vehicles.index'));

    expect($vehicle->refresh())
        ->brand->toBe('Honda')
        ->status->toBe('available');

    $this->actingAs($user)
        ->patch("/vendor/vehicles/{$vehicle->id}/status", ['status' => 'unavailable'])
        ->assertRedirect();

    expect($vehicle->refresh()->status)->toBe('unavailable');
});

test('vendor approval rejects overlapping approved rentals', function () {
    [$user, $vendor] = vendorUser('verified');
    $vehicle = vehicleForVendor($vendor, 'VAN-003');
    $customer = User::factory()->create(['role' => 'customer']);

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
        'status' => 'approved',
    ]);

    $pending = Rental::create([
        'customer_id' => User::factory()->create(['role' => 'customer'])->id,
        'vehicle_id' => $vehicle->id,
        'pickup_date' => '2026-11-12',
        'return_date' => '2026-11-14',
        'rental_days' => 2,
        'daily_price' => 1500,
        'total_amount' => 3000,
        'commission_amount' => 300,
        'vendor_amount' => 2700,
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->patch("/vendor/rentals/{$pending->id}/approve")
        ->assertStatus(422);

    expect($pending->refresh()->status)->toBe('pending');
});
