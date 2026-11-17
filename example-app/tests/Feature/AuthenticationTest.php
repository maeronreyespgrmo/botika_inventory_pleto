<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

test('guests can see login but cannot access the dashboard', function () {
    $this->get('/login')->assertOk()->assertSee('Sign in');
    $this->get('/dashboard')->assertRedirect('/login');
});

test('users can log in and view inventory', function () {
    $user = User::factory()->create();

    $this->post('/login', ['username' => $user->username, 'password' => 'password', 'remember' => '1'])
        ->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
    $this->get('/dashboard')->assertOk()->assertSee('Recently added medicines')->assertSee('No medicines yet.');
    $this->get('/login')->assertRedirect('/dashboard');
});

test('invalid credentials do not authenticate', function () {
    $user = User::factory()->create();
    $this->post('/login', ['username' => $user->username, 'password' => 'wrong'])
        ->assertSessionHasErrors('username');
    $this->assertGuest();
});

test('repeated failed logins are throttled', function () {
    $username = 'blocked@example.com';
    RateLimiter::clear($username.'|127.0.0.1');

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', ['username' => $username, 'password' => 'wrong']);
    }

    $this->post('/login', ['username' => $username, 'password' => 'wrong'])
        ->assertSessionHasErrors(['username' => 'Too many login attempts. Try again in '.RateLimiter::availableIn($username.'|127.0.0.1').' seconds.']);
    $this->assertGuest();
});

test('users can log out and lose dashboard access', function () {
    $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/login');
    $this->assertGuest();
    $this->get('/dashboard')->assertRedirect('/login');
});

test('admin command creates a hashed account', function () {
    $this->artisan('app:create-admin')
        ->expectsQuestion('Full name', 'Inventory Admin')
        ->expectsQuestion('Username', 'admin@example.com')
        ->expectsQuestion('Password (at least 12 characters)', 'a-long-password')
        ->expectsQuestion('Confirm password', 'a-long-password')
        ->assertSuccessful();

    expect(\Illuminate\Support\Facades\Hash::check('a-long-password', User::where('username', 'admin@example.com')->firstOrFail()->password))->toBeTrue();
});


