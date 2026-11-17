<?php

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('supplier routes require authentication', function () {
    $this->get('/suppliers')->assertRedirect('/login');
    $this->get('/suppliers/create')->assertRedirect('/login');
    $this->get('/suppliers/1/edit')->assertRedirect('/login');
    $this->post('/suppliers', [])->assertRedirect('/login');
    $this->put('/suppliers/1', [])->assertRedirect('/login');
    $this->delete('/suppliers/1')->assertRedirect('/login');
});

test('users can create read update and delete suppliers', function () {
    $this->actingAs(User::factory()->create());
    $this->get('/suppliers')->assertOk()->assertSee('No suppliers yet.');
    $this->get('/suppliers/create')->assertOk();
    $this->post('/suppliers', [
        'supplier_name' => 'Medical Supplies', 'contact_person' => 'Jane',
        'phone' => '+65 1234 5678', 'email' => 'jane@example.com', 'address' => '123 Main Street',
    ])->assertSessionHasNoErrors()->assertRedirect('/suppliers');
    $supplier = Supplier::firstOrFail();
    $this->get('/suppliers')->assertOk()->assertSee('Medical Supplies')->assertSee('jane@example.com');
    $this->get(route('suppliers.edit', $supplier))->assertOk()->assertSee('123 Main Street');
    $this->put(route('suppliers.update', $supplier), ['supplier_name' => 'Updated Supplies', 'email' => ''])
        ->assertSessionHasNoErrors()->assertRedirect('/suppliers');
    $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'supplier_name' => 'Updated Supplies', 'email' => null]);
    $this->delete(route('suppliers.destroy', $supplier))->assertRedirect('/suppliers');
    $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
});

test('supplier details are validated', function () {
    $this->actingAs(User::factory()->create());
    $this->post('/suppliers', ['supplier_name' => '', 'email' => 'invalid', 'phone' => str_repeat('1', 31)])
        ->assertSessionHasErrors(['supplier_name', 'email', 'phone']);
    $supplier = Supplier::create(['supplier_name' => 'Original']);
    $this->put(route('suppliers.update', $supplier), ['supplier_name' => str_repeat('x', 151)])
        ->assertSessionHasErrors('supplier_name');
    expect($supplier->fresh()->supplier_name)->toBe('Original');
});

test('suppliers with purchase records cannot be deleted', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $supplier = Supplier::create(['supplier_name' => 'Medical Supplies']);
    DB::table('purchases')->insert(['supplier_id' => $supplier->id, 'user_id' => $user->id, 'purchase_date' => now()]);
    $this->delete(route('suppliers.destroy', $supplier))->assertRedirect('/suppliers')->assertSessionHas('error');
    $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
});
