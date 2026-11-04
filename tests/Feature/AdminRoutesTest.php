<?php

use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAdminUser(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);
}

test('an admin can access the admin pages', function () {
    $admin = createAdminUser();
    $vendorUser = User::factory()->create(['role' => 'vendor']);
    $vendor = Vendor::create([
        'user_id' => $vendorUser->id,
        'business_name' => 'Test Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
    ]);
    $vehicle = Vehicle::create([
        'vendor_id' => $vendor->id,
        'brand' => 'Test',
        'model' => 'Car',
        'year' => 2025,
        'plate_number' => 'TEST-001',
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1500,
        'security_deposit' => 5000,
    ]);
    $rental = Rental::create([
        'customer_id' => User::factory()->create(['role' => 'customer'])->id,
        'vehicle_id' => $vehicle->id,
        'pickup_date' => now()->toDateString(),
        'return_date' => now()->addDay()->toDateString(),
        'rental_days' => 1,
        'daily_price' => 1500,
        'total_amount' => 1500,
        'commission_amount' => 150,
        'vendor_amount' => 1350,
    ]);

    $this->actingAs($admin);

    $this->get(route('admin.dashboard'))->assertOk();
    $this->get('/admin/vendors')->assertOk();
    $this->get("/admin/vendors/{$vendor->id}")->assertOk();
    $this->get('/admin/users')->assertOk();
    $this->get('/admin/vehicles')->assertOk();
    $this->get('/admin/rentals')->assertOk();
    $this->get("/admin/rentals/{$rental->id}")->assertOk();
});

test('an admin can verify vendors and update user status', function () {
    $admin = createAdminUser();
    $vendorUser = User::factory()->create(['role' => 'vendor']);
    $vendor = Vendor::create([
        'user_id' => $vendorUser->id,
        'business_name' => 'Test Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
    ]);
    $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

    $this->actingAs($admin);

    $this->patch("/admin/vendors/{$vendor->id}/verify")
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($vendor->refresh()->verification_status)->toBe('verified')
        ->and($vendor->verified_at)->not->toBeNull();

    $this->patch("/admin/users/{$customer->id}/status", ['status' => 'suspended'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($customer->refresh()->status)->toBe('suspended');
});

test('an admin can reject a vendor', function () {
    $admin = createAdminUser();
    $vendorUser = User::factory()->create(['role' => 'vendor']);
    $vendor = Vendor::create([
        'user_id' => $vendorUser->id,
        'business_name' => 'Test Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'verification_status' => 'pending',
    ]);

    $this->actingAs($admin);

    $this->patch("/admin/vendors/{$vendor->id}/reject")
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($vendor->refresh()->verification_status)->toBe('rejected')
        ->and($vendor->verified_at)->toBeNull();
});
