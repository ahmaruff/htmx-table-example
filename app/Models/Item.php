<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'description',
        'category',
        'brand',
        'price',
        'stock',
        'unit',
        'status',
        'is_active',
        'weight',
        'manufactured_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'weight' => 'decimal:2',
        'manufactured_at' => 'date',
    ];

    public const CATEGORIES = [
        'Electronics',
        'Furniture',
        'Office Supplies',
        'Accessories',
        'Appliances',
        'Tools',
    ];

    public const BRANDS = [
        'Logitech',
        'Samsung',
        'Sony',
        'Acer',
        'Xiaomi',
        'IKEA',
        'HP',
        'Lenovo',
        'Canon',
        'Philips',
    ];

    public const UNITS = [
        'pcs',
        'box',
        'pack',
        'set',
        'kg',
    ];

    public const STATUSES = [
        'available',
        'low_stock',
        'out_of_stock',
        'discontinued',
    ];
}
