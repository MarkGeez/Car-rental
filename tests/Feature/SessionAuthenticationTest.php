<?php

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a visitor can register with a web session', function () {
    $response = $this->post('/register', [
        'name' => 'Jane Customer',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'customer',
    ]);

    $response
        ->assertRedirect(route('login'))
        ->assertSessionHas('success');

    expect(User::where('email', 'jane@example.com')->exists())->toBeTrue();
});

test('an active user can log in and receives a session redirect', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => 'password',
        'role' => 'customer',
        'status' => 'active',
    ]);

    $response = $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'password',
    ]);

    $response
        ->assertRedirect('/user/dashboard')
        ->assertSessionHasNoErrors();

    $this->assertAuthenticatedAs($user);
});

test('a vendor without a business profile is sent to profile setup after login', function () {
    $user = User::factory()->create([
        'email' => 'vendor@example.com',
        'password' => 'password',
        'role' => 'vendor',
        'status' => 'active',
    ]);

    $response = $this->post('/login', [
        'email' => 'vendor@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('vendor.me'));
    expect(Vendor::where('user_id', $user->id)->exists())->toBeFalse();
});

test('a suspended user cannot log in', function () {
    User::factory()->create([
        'email' => 'suspended@example.com',
        'password' => 'password',
        'role' => 'customer',
        'status' => 'suspended',
    ]);

    $response = $this->post('/login', [
        'email' => 'suspended@example.com',
        'password' => 'password',
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('an authenticated user can log out and invalidate the session', function () {
    $user = User::factory()->create([
        'role' => 'customer',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect(route('home'));
    $this->assertGuest();
});
