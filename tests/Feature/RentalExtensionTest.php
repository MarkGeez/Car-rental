<?php

use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function extensionFixture(): array
{
    $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
    $vendorUser = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
    $vendor = Vendor::create([
        'user_id' => $vendorUser->id,
        'business_name' => 'Extension Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'verification_status' => 'verified',
    ]);
    $vehicle = Vehicle::create([
        'vendor_id' => $vendor->id,
        'brand' => 'Toyota',
        'model' => 'Vios',
        'year' => 2026,
        'plate_number' => 'EXT-001',
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1000,
        'security_deposit' => 5000,
        'status' => 'available',
    ]);
    $rental = Rental::create([
        'customer_id' => $customer->id,
        'vehicle_id' => $vehicle->id,
        'pickup_date' => '2026-12-08',
        'return_date' => '2026-12-10',
        'rental_days' => 3,
        'daily_price' => 1000,
        'total_amount' => 3000,
        'commission_amount' => 300,
        'vendor_amount' => 2700,
        'status' => 'approved',
    ]);

    return [$customer, $vendorUser, $vendor, $vehicle, $rental];
}

it('creates a linked extension rental with extra-only amounts', function () {
    [$customer, , , , $rental] = extensionFixture();

    $this->actingAs($customer)
        ->post(route('user.rentals.extend', $rental), [
            'return_date' => '2026-12-12',
        ])
        ->assertRedirect();

    $extension = Rental::query()->where('extended_from_id', $rental->id)->firstOrFail();

    expect($extension->customer_id)->toBe($customer->id)
        ->and($extension->vehicle_id)->toBe($rental->vehicle_id)
        ->and($extension->pickup_date->toDateString())->toBe('2026-12-10')
        ->and($extension->return_date->toDateString())->toBe('2026-12-12')
        ->and($extension->rental_days)->toBe(2)
        ->and((float) $extension->total_amount)->toBe(2000.0)
        ->and((float) $extension->commission_amount)->toBe(200.0)
        ->and((float) $extension->vendor_amount)->toBe(1800.0)
        ->and($extension->status)->toBe('pending');
});

it('rejects an extension when another booking overlaps the extra dates', function () {
    [$customer, , , $vehicle, $rental] = extensionFixture();
    Rental::create([
        'customer_id' => User::factory()->create(['role' => 'customer', 'status' => 'active'])->id,
        'vehicle_id' => $vehicle->id,
        'pickup_date' => '2026-12-11',
        'return_date' => '2026-12-13',
        'rental_days' => 3,
        'daily_price' => 1000,
        'total_amount' => 3000,
        'commission_amount' => 300,
        'vendor_amount' => 2700,
        'status' => 'approved',
    ]);

    $this->actingAs($customer)
        ->from(route('user.rentals.show', $rental))
        ->post(route('user.rentals.extend', $rental), [
            'return_date' => '2026-12-12',
        ])
        ->assertRedirect(route('user.rentals.show', $rental))
        ->assertSessionHasErrors(['return_date']);

    expect(Rental::query()->where('extended_from_id', $rental->id)->exists())->toBeFalse();
});

it('prevents a second pending extension on the same rental', function () {
    [$customer, , , , $rental] = extensionFixture();
    $rental->extensions()->create([
        'customer_id' => $customer->id,
        'vehicle_id' => $rental->vehicle_id,
        'pickup_date' => '2026-12-10',
        'return_date' => '2026-12-11',
        'rental_days' => 1,
        'daily_price' => 1000,
        'total_amount' => 1000,
        'commission_amount' => 100,
        'vendor_amount' => 900,
        'status' => 'pending',
    ]);

    $this->actingAs($customer)
        ->from(route('user.rentals.show', $rental))
        ->post(route('user.rentals.extend', $rental), [
            'return_date' => '2026-12-12',
        ])
        ->assertStatus(422);

    expect(Rental::query()->where('extended_from_id', $rental->id)->count())->toBe(1);
});

it('does not allow another customer to extend the rental', function () {
    [, , , , $rental] = extensionFixture();
    $otherCustomer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

    $this->actingAs($otherCustomer)
        ->post(route('user.rentals.extend', $rental), [
            'return_date' => '2026-12-12',
        ])
        ->assertNotFound();
});

it('allows the vendor to approve an extension without conflicting with its source rental', function () {
    [$customer, $vendorUser, , , $rental] = extensionFixture();
    $extension = $rental->extensions()->create([
        'customer_id' => $customer->id,
        'vehicle_id' => $rental->vehicle_id,
        'pickup_date' => '2026-12-10',
        'return_date' => '2026-12-12',
        'rental_days' => 2,
        'daily_price' => 1000,
        'total_amount' => 2000,
        'commission_amount' => 200,
        'vendor_amount' => 1800,
        'status' => 'pending',
    ]);
    $extension->payment()->create([
        'amount' => 2000,
        'payment_method' => 'gcash',
        'transaction_reference' => 'EXT-TEST-001',
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);

    $this->actingAs($vendorUser)
        ->patch("/vendor/rentals/{$extension->id}/approve")
        ->assertRedirect();

    expect($extension->fresh()->status)->toBe('approved');
});
