<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's administrator accounts.
     */
    public function run(): void
    {
        $administrators = [
            [
                'name' => env('ADMIN_ONE_NAME', 'Entrep Administrator'),
                'email' => env('ADMIN_ONE_EMAIL', 'admin@example.com'),
                'password' => env('ADMIN_ONE_PASSWORD', 'password'),
            ],
            [
                'name' => env('ADMIN_TWO_NAME', 'Entrep Operations'),
                'email' => env('ADMIN_TWO_EMAIL', 'operations@example.com'),
                'password' => env('ADMIN_TWO_PASSWORD', 'password'),
            ],
        ];

        foreach ($administrators as $administrator) {
            User::updateOrCreate(
                ['email' => $administrator['email']],
                [
                    'name' => $administrator['name'],
                    'password' => Hash::make($administrator['password']),
                    'role' => 'admin',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
