<?php

use App\Models\Category;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function medicineData(array $overrides = []): array
{
    return array_replace([
        'generic_name' => 'Paracetamol', 'brand_name' => 'Example brand',
        'dosage_strength' => '500 mg', 'dosage_form' => 'Tablet', 'barcode' => 'MED001',
        'unit' => 'tablet', 'selling_price' => '2.50', 'reorder_level' => 10,
        'requires_prescription' => 1, 'is_active' => 1,
    ], $overrides);
}

test('medicine routes require authentication', function () {
    $this->get('/medicines')->assertRedirect('/login');
    $this->get('/medicines/create')->assertRedirect('/login');
    $this->get('/medicines/1/edit')->assertRedirect('/login');
    $this->post('/medicines', [])->assertRedirect('/login');
    $this->put('/medicines/1', [])->assertRedirect('/login');
    $this->delete('/medicines/1')->assertRedirect('/login');
});

test('users can create list edit and delete medicines', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::create(['category_name' => 'Pain relief']);
    $this->get('/medicines')->assertOk()->assertSee('No medicines yet.');
    $this->get('/medicines/create')->assertOk()->assertSee('Pain relief');
    $this->post('/medicines', medicineData(['category_id' => $category->id]))->assertSessionHasNoErrors()->assertRedirect('/medicines');
    $medicine = Medicine::firstOrFail();
    expect($medicine->selling_price)->toBe('2.50');
    $this->get('/medicines')->assertOk()->assertSee('Paracetamol')->assertSee('Pain relief')->assertSee('Prescription required');
    $this->get(route('medicines.edit', $medicine))->assertOk()->assertSee('500 mg');
    $this->put(route('medicines.update', $medicine), medicineData(['is_active' => 0, 'requires_prescription' => 0]))->assertSessionHasNoErrors()->assertRedirect('/medicines');
    expect($medicine->fresh()->is_active)->toBeFalse();
    expect($medicine->fresh()->requires_prescription)->toBeFalse();
    $this->delete(route('medicines.destroy', $medicine))->assertRedirect('/medicines');
    $this->assertDatabaseMissing('medicines', ['id' => $medicine->id]);
});

test('invalid medicine fields and duplicate barcodes are rejected', function () {
    $this->actingAs(User::factory()->create());
    Medicine::create(medicineData());
    $this->post('/medicines', medicineData())->assertSessionHasErrors('barcode');
    $this->post('/medicines', medicineData([
        'generic_name' => '', 'unit' => '', 'selling_price' => '-1', 'reorder_level' => '1.5',
        'category_id' => 999, 'barcode' => null,
    ]))->assertSessionHasErrors(['generic_name', 'unit', 'selling_price', 'reorder_level', 'category_id']);
    $this->post('/medicines', medicineData(['barcode' => null, 'selling_price' => '1.234']))->assertSessionHasErrors('selling_price');
});

test('optional medicine fields can be empty', function () {
    $this->actingAs(User::factory()->create());
    foreach (range(1, 2) as $i) {
        $this->post('/medicines', medicineData(['barcode' => '', 'brand_name' => '', 'dosage_strength' => '', 'dosage_form' => '', 'category_id' => '']))
            ->assertSessionHasNoErrors()->assertRedirect('/medicines');
    }
    expect(Medicine::count())->toBe(2);
});

test('medicine with stock batches cannot be deleted', function () {
    $this->actingAs(User::factory()->create());
    $medicine = Medicine::create(medicineData());
    DB::table('stock_batches')->insert([
        'medicine_id' => $medicine->id, 'batch_number' => 'B001', 'expiration_date' => '2028-01-01',
        'quantity_on_hand' => 10, 'cost_price' => 1, 'received_date' => '2026-11-17',
    ]);
    $this->delete(route('medicines.destroy', $medicine))->assertRedirect('/medicines')->assertSessionHas('error');
    $this->assertDatabaseHas('medicines', ['id' => $medicine->id]);
});
