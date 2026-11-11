<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login returns too many requests after five attempts for an email and IP address', function () {
    User::factory()->create([
        'email' => 'customer@example.com',
        'status' => 'active',
    ]);

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    $this->post('/login', [
        'email' => 'customer@example.com',
        'password' => 'wrong-password',
    ])->assertTooManyRequests();
});

test('registration returns too many requests after three attempts from an IP address', function () {
    for ($attempt = 0; $attempt < 3; $attempt++) {
        $this->post('/register', [
            'name' => 'Test Customer',
            'email' => "customer{$attempt}@example.com",
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'customer',
        ])->assertRedirectToRoute('login');
    }

    $this->post('/register', [
        'name' => 'Test Customer',
        'email' => 'customer-final@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'customer',
    ])->assertTooManyRequests();
});

test('authenticated route groups return too many requests after sixty requests per user', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'status' => 'active',
    ]);

    $this->actingAs($customer);

    for ($request = 0; $request < 60; $request++) {
        $this->get('/user/me')->assertOk();
    }

    $this->get('/user/me')->assertTooManyRequests();
});
