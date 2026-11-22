<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Item::CATEGORIES;
        $brands = Item::BRANDS;
        $units = Item::UNITS;
        $statuses = Item::STATUSES;

        $items = [
            'Wireless Mouse',
            'Mechanical Keyboard',
            'USB-C Hub',
            'Monitor 24 Inch',
            'Laptop Stand',
            'Office Chair',
            'Desk Lamp',
            'External SSD',
            'Webcam HD',
            'Headphones',
            'Bluetooth Speaker',
            'Power Bank',
            'HDMI Cable',
            'Laptop Backpack',
            'Desk Organizer',
            'Printer',
            'Wireless Router',
            'Smart Watch',
            'Microphone',
            'Drawing Tablet',
        ];

        foreach (range(1, 500) as $i) {
            $stock = fake()->numberBetween(0, 500);

            Item::create([
                'sku' => 'ITM-'.str_pad($i, 5, '0', STR_PAD_LEFT),
                'name' => fake()->randomElement($items).' '.fake()->word(),
                'description' => fake()->optional(0.8)->sentence(10),
                'category' => fake()->randomElement($categories),
                'brand' => fake()->randomElement($brands),
                'price' => fake()->randomFloat(2, 10000, 15000000),
                'stock' => $stock,
                'unit' => fake()->randomElement($units),
                'status' => fake()->randomElement($statuses),
                'is_active' => fake()->boolean(85),
                'weight' => fake()->optional(0.7)->randomFloat(2, 0.1, 50),
                'manufactured_at' => fake()->optional(0.8)->dateTimeBetween('-2 years', 'now'),
            ]);
        }
    }
}
