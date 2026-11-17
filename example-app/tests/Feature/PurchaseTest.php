<?php

use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('purchase routes require authentication', function () {
    $this->get('/purchases')->assertRedirect('/login');
    $this->get('/purchases/create')->assertRedirect('/login');
    $this->get('/purchases/1/edit')->assertRedirect('/login');
    $this->post('/purchases', [])->assertRedirect('/login');
    $this->put('/purchases/1', [])->assertRedirect('/login');
    $this->delete('/purchases/1')->assertRedirect('/login');
});

test('users can create read update and delete purchase records', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->get('/purchases')->assertOk()->assertSee('No purchases yet.');
    $this->get('/purchases/create')->assertOk()->assertSee('before creating a purchase');
    $supplier = Supplier::create(['supplier_name' => 'Medical Supplies']);
    $data = ['supplier_id' => $supplier->id, 'purchase_date' => '2026-11-17T14:30', 'reference_number' => 'PO-001', 'status' => 'pending'];
    $this->post('/purchases', $data + ['user_id' => 999])->assertSessionHasNoErrors()->assertRedirect('/purchases');
    $purchase = Purchase::firstOrFail();
    expect($purchase->user_id)->toBe($user->id);
    $this->get('/purchases')->assertOk()->assertSee('PO-001')->assertSee('Medical Supplies');
    $this->get(route('purchases.edit', $purchase))->assertOk()->assertSee('2026-11-17T14:30');
    $this->put(route('purchases.update', $purchase), array_replace($data, ['status' => 'received']))->assertSessionHasNoErrors()->assertRedirect('/purchases');
    expect($purchase->fresh()->status)->toBe('received');
    expect(DB::table('stock_batches')->count())->toBe(0);
    $this->delete(route('purchases.destroy', $purchase))->assertRedirect('/purchases');
    $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
});

test('purchase details are validated', function () {
    $this->actingAs(User::factory()->create());
    $this->post('/purchases', ['supplier_id' => 999, 'purchase_date' => 'invalid', 'status' => 'invalid', 'reference_number' => str_repeat('x', 101)])
        ->assertSessionHasErrors(['supplier_id', 'purchase_date', 'status', 'reference_number']);
    expect(Purchase::count())->toBe(0);
});

test('purchases with items cannot be edited or deleted', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $supplier = Supplier::create(['supplier_name' => 'Medical Supplies']);
    $purchase = new Purchase(['supplier_id' => $supplier->id, 'purchase_date' => now(), 'status' => 'received']);
    $purchase->user_id = $user->id;
    $purchase->save();
    $medicine = Medicine::create(['generic_name' => 'Example', 'unit' => 'tablet', 'selling_price' => 2]);
    $batchId = DB::table('stock_batches')->insertGetId(['medicine_id' => $medicine->id, 'batch_number' => 'B1', 'expiration_date' => '2028-01-01', 'received_date' => '2026-11-17', 'cost_price' => 1]);
    DB::table('purchase_items')->insert(['purchase_id' => $purchase->id, 'batch_id' => $batchId, 'quantity' => 5, 'unit_cost' => 1]);
    $this->put(route('purchases.update', $purchase), ['supplier_id' => $supplier->id, 'purchase_date' => '2026-11-17T12:00', 'status' => 'cancelled'])->assertRedirect('/purchases')->assertSessionHas('error');
    expect($purchase->fresh()->status)->toBe('received');
    $this->delete(route('purchases.destroy', $purchase))->assertRedirect('/purchases')->assertSessionHas('error');
    $this->assertDatabaseHas('purchases', ['id' => $purchase->id]);
    expect(DB::table('purchase_items')->count())->toBe(1);
});
