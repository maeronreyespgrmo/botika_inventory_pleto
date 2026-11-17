<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('category routes require authentication', function () {
    $this->get('/categories')->assertRedirect('/login');
    $this->get('/categories/create')->assertRedirect('/login');
    $this->post('/categories', [])->assertRedirect('/login');
    $this->put('/categories/1', [])->assertRedirect('/login');
    $this->delete('/categories/1')->assertRedirect('/login');
});

test('users can create read update and delete a category', function () {
    $this->actingAs(User::factory()->create());
    $this->get('/categories')->assertOk()->assertSee('No categories yet.');
    $this->get('/categories/create')->assertOk();
    $this->post('/categories', ['category_name' => 'Pain relief', 'description' => 'Pain medicines'])
        ->assertRedirect('/categories');
    $category = Category::firstOrFail();
    $this->get('/categories')->assertSee('Pain relief');
    $this->get(route('categories.edit', $category))->assertOk()->assertSee('Pain medicines');
    $this->put(route('categories.update', $category), ['category_name' => 'Pain relief', 'description' => 'Updated'])
        ->assertSessionHasNoErrors()->assertRedirect('/categories');
    $this->assertDatabaseHas('categories', ['id' => $category->id, 'description' => 'Updated']);
    $this->delete(route('categories.destroy', $category))->assertRedirect('/categories');
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('category names are required and unique', function () {
    $this->actingAs(User::factory()->create());
    Category::create(['category_name' => 'Antibiotics']);
    $this->post('/categories', ['category_name' => ''])->assertSessionHasErrors('category_name');
    $this->post('/categories', ['category_name' => 'Antibiotics'])->assertSessionHasErrors('category_name');
    $other = Category::create(['category_name' => 'Vitamins']);
    $this->put(route('categories.update', $other), ['category_name' => 'Antibiotics'])->assertSessionHasErrors('category_name');
});

test('categories used by medicines cannot be deleted', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::create(['category_name' => 'Pain relief']);
    DB::table('medicines')->insert([
        'category_id' => $category->id,
        'generic_name' => 'Paracetamol',
        'unit' => 'tablet',
        'selling_price' => 1,
    ]);
    $this->delete(route('categories.destroy', $category))->assertRedirect('/categories')->assertSessionHas('error');
    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});
