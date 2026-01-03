<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop',
                'description' => 'High performance laptop for professionals',
                'price' => 999.99,
                'stock_quantity' => 15,
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse',
                'price' => 29.99,
                'stock_quantity' => 50,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB mechanical gaming keyboard',
                'price' => 79.99,
                'stock_quantity' => 3, // Low stock!
            ],
            [
                'name' => 'USB-C Hub',
                'description' => '7-in-1 USB-C hub adapter',
                'price' => 49.99,
                'stock_quantity' => 25,
            ],
            [
                'name' => 'Webcam HD',
                'description' => '1080p HD webcam with microphone',
                'price' => 59.99,
                'stock_quantity' => 2, // Low stock!
            ],
            [
                'name' => 'Monitor 27"',
                'description' => '4K UHD 27-inch monitor',
                'price' => 399.99,
                'stock_quantity' => 8,
            ],
            [
                'name' => 'Desk Lamp',
                'description' => 'LED desk lamp with adjustable brightness',
                'price' => 34.99,
                'stock_quantity' => 30,
            ],
            [
                'name' => 'Headphones',
                'description' => 'Noise-cancelling wireless headphones',
                'price' => 149.99,
                'stock_quantity' => 4, // Low stock!
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}