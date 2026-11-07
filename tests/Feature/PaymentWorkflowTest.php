<?php

use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function paymentFixture(): array
{
    $vendorUser = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
    $vendor = Vendor::create([
        'user_id' => $vendorUser->id,
        'business_name' => 'QR Fleet',
        'address' => '1 Main Street',
        'city' => 'Manila',
        'payment_qr_code' => 'payment-qr-codes/qr.png',
        'verification_status' => 'verified',
    ]);
    $vehicle = $vendor->vehicles()->create([
        'brand' => 'Toyota',
        'model' => 'Vios',
        'year' => 2025,
        'plate_number' => 'PAY-001',
        'vehicle_type' => 'Sedan',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_price' => 1500,
        'security_deposit' => 5000,
        'status' => 'available',
    ]);

    return [$vendorUser, $vendor, $vehicle];
}

function paymentRental(Vehicle $vehicle, User $customer, string $plate = 'PAY-001'): Rental
{
    return Rental::create([
        'customer_id' => $customer->id,
        'vehicle_id' => $vehicle->id,
        'pickup_date' => '2026-12-10',
        'return_date' => '2026-12-12',
        'rental_days' => 3,
        'daily_price' => 1500,
        'total_amount' => 4500,
        'commission_amount' => 450,
        'vendor_amount' => 4050,
        'status' => 'pending',
    ]);
}

test('a vendor can upload a payment QR code', function () {
    Storage::fake('public');
    $vendorUser = User::factory()->create(['role' => 'vendor', 'status' => 'active']);

    $this->actingAs($vendorUser)
        ->post('/vendor/profile', [
            'business_name' => 'QR Upload Fleet',
            'address' => '1 Main Street',
            'city' => 'Manila',
            'business_permit' => UploadedFile::fake()->image('permit.jpg'),
            'payment_qr_code' => UploadedFile::fake()->image('qr.png'),
        ])
        ->assertRedirect(route('vendor.me'));

    $vendor = $vendorUser->fresh()->vendor;
    expect($vendor->payment_qr_code)->not->toBeNull();
    Storage::disk('public')->assertExists($vendor->payment_qr_code);
});

test('a renter can upload payment proof and a vendor can reserve the first paid rental', function () {
    Storage::fake('public');
    [$vendorUser, , $vehicle] = paymentFixture();
    $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
    $rental = paymentRental($vehicle, $customer);

    $this->actingAs($customer)
        ->get("/user/rentals/{$rental->id}/payment")
        ->assertOk()
        ->assertSee('Vendor payment QR code');

    $this->actingAs($customer)
        ->post("/user/rentals/{$rental->id}/payment", [
            'payment_method' => 'gcash',
            'proof' => UploadedFile::fake()->image('proof.png'),
        ])
        ->assertRedirect(route('user.rentals.show', $rental));

    expect($rental->fresh()->payment->payment_status)->toBe('pending');

    $this->actingAs($vendorUser)
        ->get("/vendor/rentals/{$rental->id}")
        ->assertOk()
        ->assertSee('Payment proof');

    $this->actingAs($vendorUser)
        ->patch("/vendor/rentals/{$rental->id}/payment/paid")
        ->assertRedirect();

    expect($rental->refresh()->status)->toBe('approved')
        ->and($rental->payment->payment_status)->toBe('paid');
});

test('a duplicate payment cannot reserve an already paid booking and can be refunded', function () {
    Storage::fake('public');
    [$vendorUser, , $vehicle] = paymentFixture();
    $firstCustomer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
    $secondCustomer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
    $firstRental = paymentRental($vehicle, $firstCustomer);
    $secondRental = paymentRental($vehicle, $secondCustomer, 'PAY-002');

    foreach ([$firstRental, $secondRental] as $rental) {
        $this->actingAs($rental->customer)->post("/user/rentals/{$rental->id}/payment", [
            'payment_method' => 'gcash',
            'proof' => UploadedFile::fake()->image("proof-{$rental->id}.png"),
        ]);
    }

    $this->actingAs($vendorUser)->patch("/vendor/rentals/{$firstRental->id}/payment/paid");

    $this->actingAs($vendorUser)
        ->patch("/vendor/rentals/{$secondRental->id}/payment/paid")
        ->assertStatus(422);

    $this->actingAs($vendorUser)
        ->patch("/vendor/rentals/{$secondRental->id}/payment/refund")
        ->assertRedirect();

    expect($secondRental->refresh()->status)->toBe('cancelled')
        ->and($secondRental->payment->payment_status)->toBe('refunded');
});
