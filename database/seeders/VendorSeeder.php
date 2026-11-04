<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use RuntimeException;

class VendorSeeder extends Seeder
{
    /**
     * Seed a vendor profile for an existing vendor user.
     */
    public function run(): void
    {
        $vendorEmail = env('VENDOR_EMAIL');
        $vendorUser = User::query()
            ->where('role', 'vendor')
            ->when($vendorEmail, fn ($query) => $query->where('email', $vendorEmail))
            ->first();

        if (! $vendorUser) {
            $identifier = $vendorEmail
                ? " with email [{$vendorEmail}]"
                : '';

            throw new RuntimeException("No vendor user{$identifier} was found.");
        }

        Vendor::updateOrCreate(
            ['user_id' => $vendorUser->id],
            [
                'business_name' => env('VENDOR_BUSINESS_NAME', "{$vendorUser->name}'s Car Rental"),
                'description' => env('VENDOR_DESCRIPTION', 'Reliable and affordable vehicle rentals.'),
                'address' => env('VENDOR_ADDRESS', '1 Main Street'),
                'city' => env('VENDOR_CITY', 'Manila'),
                'business_permit' => env('VENDOR_BUSINESS_PERMIT'),
                'verification_status' => 'verified',
                'verified_at' => now(),
            ],
        );
    }
}
