<?php

use App\Models\Item;
use App\Services\ItemDatatableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

function createItem(array $overrides = []): Item
{
    return Item::create(array_merge([
        'sku' => fake()->unique()->bothify('ITM-#####'),
        'name' => fake()->word(),
        'category' => 'Electronics',
        'brand' => 'Logitech',
        'price' => 1000,
        'stock' => 10,
        'unit' => 'pcs',
        'status' => 'available',
        'is_active' => true,
    ], $overrides));
}

test('items index page renders', function () {
    createItem();

    $this->get(route('items.index'))
        ->assertOk()
        ->assertSee('Items');
});

test('htmx requests return only the table fragment', function () {
    createItem();

    $this->get(route('items.index'), ['HX-Request' => 'true'])
        ->assertOk()
        ->assertSee('id="item-table"', false)
        ->assertSee('htmx-table-wrapper', false)
        ->assertDontSee('All categories');
});

test('filter form targets the rendered table wrapper', function () {
    createItem();

    $this->get(route('items.index'))
        ->assertOk()
        ->assertSee('id="item-table"', false)
        ->assertSee('hx-target="#item-table"', false);
});

test('filter params are applied to the table fragment', function () {
    createItem(['name' => 'Furniture Item', 'category' => 'Furniture']);
    createItem(['name' => 'Electronics Item', 'category' => 'Electronics']);

    $this->get(route('items.index', ['category' => 'Furniture']), ['HX-Request' => 'true'])
        ->assertOk()
        ->assertSee('Furniture Item')
        ->assertDontSee('Electronics Item');
});

test('search filters the table fragment', function () {
    createItem(['name' => 'Searchable Alpha']);
    createItem(['name' => 'Hidden Beta']);

    $this->get(route('items.index', ['search' => 'Alpha']), ['HX-Request' => 'true'])
        ->assertOk()
        ->assertSee('Searchable Alpha')
        ->assertDontSee('Hidden Beta');
});

test('sortable columns render sort controls', function () {
    createItem();

    $this->get(route('items.index'), ['HX-Request' => 'true'])
        ->assertOk()
        ->assertSee('htmx-table-sort-link', false)
        ->assertSee('sort=name', false);
});

test('build query filters items by category brand status and active flag', function () {
    createItem(['sku' => 'ITM-00001']);
    createItem(['sku' => 'ITM-00002', 'category' => 'Furniture', 'brand' => 'IKEA', 'status' => 'low_stock', 'is_active' => false]);
    createItem(['sku' => 'ITM-00003', 'brand' => 'Sony', 'status' => 'out_of_stock']);

    $service = new ItemDatatableService;

    expect($service->buildQuery(new Request(['category' => 'Electronics']))->count())->toBe(2);
    expect($service->buildQuery(new Request(['brand' => 'Sony']))->count())->toBe(1);
    expect($service->buildQuery(new Request(['status' => 'low_stock']))->count())->toBe(1);
    expect($service->buildQuery(new Request(['is_active' => '0']))->count())->toBe(1);
    expect($service->buildQuery(new Request(['category' => 'Unknown']))->count())->toBe(3);
});

test('filters returns only allowed values', function () {
    $service = new ItemDatatableService;

    $filters = $service->filters(new Request([
        'category' => 'Electronics',
        'brand' => 'Unknown',
        'status' => 'available',
        'is_active' => '1',
    ]));

    expect($filters)->toBe([
        'category' => 'Electronics',
        'brand' => '',
        'status' => 'available',
        'is_active' => '1',
    ]);
});
