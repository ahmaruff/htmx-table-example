<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeEditableItem(array $overrides = []): Item
{
    return Item::create(array_merge([
        'sku' => fake()->unique()->bothify('ITM-#####'),
        'name' => 'Original Name',
        'description' => 'Original description',
        'category' => 'Electronics',
        'brand' => 'Logitech',
        'price' => 1234.56,
        'stock' => 10,
        'unit' => 'pcs',
        'status' => 'available',
        'is_active' => true,
        'weight' => 2.5,
        'manufactured_at' => '2025-01-15',
    ], $overrides));
}

test('edit row renders current values in a single row', function () {
    $item = makeEditableItem(['name' => 'Editable Item']);

    $this->get(route('items.edit', $item), ['HX-Request' => 'true'])
        ->assertOk()
        ->assertSee('hx-put="'.route('items.update', $item).'"', false)
        ->assertSee('hx-include="closest tr"', false)
        ->assertSee('hx-target="closest tr"', false)
        ->assertSee('name="sku"', false)
        ->assertSee('value="Editable Item"', false)
        ->assertSee('Original description')
        ->assertSee('value="2025-01-15"', false)
        ->assertDontSee('<form', false);
});

test('update renders sku validation error when sku is missing', function () {
    $item = makeEditableItem();

    $this->put(route('items.update', $item), [
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'category' => 'Furniture',
        'brand' => 'IKEA',
        'price' => '9876.54',
        'stock' => 3,
        'unit' => 'box',
        'status' => 'low_stock',
        'is_active' => '1',
        'weight' => '4.25',
        'manufactured_at' => '2025-02-20',
    ], ['HX-Request' => 'true'])
        ->assertStatus(422)
        ->assertSee('The sku field is required.')
        ->assertSee('name="sku"', false);
});

test('update persists values and returns the read row', function () {
    $item = makeEditableItem();

    $this->put(route('items.update', $item), [
        'sku' => $item->sku,
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'category' => 'Furniture',
        'brand' => 'IKEA',
        'price' => '9876.54',
        'stock' => 3,
        'unit' => 'box',
        'status' => 'low_stock',
        'is_active' => '1',
        'weight' => '4.25',
        'manufactured_at' => '2025-02-20',
    ], ['HX-Request' => 'true'])
        ->assertOk()
        ->assertSee('Updated Name')
        ->assertDontSee('hx-put', false);

    $item->refresh();

    expect($item->name)->toBe('Updated Name');
    expect($item->price)->toBe('9876.54');
    expect($item->manufactured_at->format('Y-m-d'))->toBe('2025-02-20');
});

test('update returns validation errors as an editable fragment', function () {
    $item = makeEditableItem();

    $this->put(route('items.update', $item), [
        'sku' => $item->sku,
        'name' => '',
        'description' => 'Changed description',
        'category' => 'Electronics',
        'brand' => 'Logitech',
        'price' => '1234.56',
        'stock' => 10,
        'unit' => 'pcs',
        'status' => 'available',
        'is_active' => '1',
        'weight' => '2.50',
        'manufactured_at' => '2025-01-15',
    ], ['HX-Request' => 'true'])
        ->assertStatus(422)
        ->assertSee('hx-put="'.route('items.update', $item).'"', false)
        ->assertSee('The name field is required.')
        ->assertSee('Changed description');

    expect($item->refresh()->name)->toBe('Original Name');
});

test('update allows keeping the same sku', function () {
    $item = makeEditableItem();

    $this->put(route('items.update', $item), [
        'sku' => $item->sku,
        'name' => $item->name,
        'description' => $item->description,
        'category' => $item->category,
        'brand' => $item->brand,
        'price' => $item->price,
        'stock' => $item->stock,
        'unit' => $item->unit,
        'status' => $item->status,
        'is_active' => '1',
        'weight' => $item->weight,
        'manufactured_at' => $item->manufactured_at->format('Y-m-d'),
    ], ['HX-Request' => 'true'])
        ->assertOk();

    expect($item->refresh()->sku)->toBe($item->sku);
});
