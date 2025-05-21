<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure we're using the tenant connection
        DB::setDefaultConnection('tenant');
        
        // Get all sections
        $sections = Section::all();

        $products = [
            // Breads
            [
                'section_name' => 'Breads',
                'products' => [
                    [
                        'name' => 'Sourdough Bread',
                        'description' => 'Traditional sourdough bread with crispy crust',
                        'price' => 5.99,
                        'stock' => 20,
                        'status' => 'active',
                    ],
                    [
                        'name' => 'Whole Wheat Bread',
                        'description' => 'Healthy whole wheat bread',
                        'price' => 4.99,
                        'stock' => 15,
                        'status' => 'active',
                    ],
                ]
            ],
            // Pastries
            [
                'section_name' => 'Pastries',
                'products' => [
                    [
                        'name' => 'Butter Croissant',
                        'description' => 'Flaky butter croissant',
                        'price' => 3.99,
                        'stock' => 30,
                        'status' => 'active',
                    ],
                    [
                        'name' => 'Danish Pastry',
                        'description' => 'Sweet danish with fruit filling',
                        'price' => 4.50,
                        'stock' => 25,
                        'status' => 'active',
                    ],
                ]
            ],
            // Cakes
            [
                'section_name' => 'Cakes',
                'products' => [
                    [
                        'name' => 'Chocolate Cake',
                        'description' => 'Rich chocolate layer cake',
                        'price' => 28.99,
                        'stock' => 5,
                        'status' => 'active',
                    ],
                    [
                        'name' => 'Carrot Cake',
                        'description' => 'Moist carrot cake with cream cheese frosting',
                        'price' => 26.99,
                        'stock' => 4,
                        'status' => 'active',
                    ],
                ]
            ],
            // Cookies
            [
                'section_name' => 'Cookies',
                'products' => [
                    [
                        'name' => 'Chocolate Chip Cookies',
                        'description' => 'Classic chocolate chip cookies',
                        'price' => 1.99,
                        'stock' => 50,
                        'status' => 'active',
                    ],
                    [
                        'name' => 'Oatmeal Raisin Cookies',
                        'description' => 'Healthy oatmeal raisin cookies',
                        'price' => 1.99,
                        'stock' => 40,
                        'status' => 'active',
                    ],
                ]
            ],
            // Desserts
            [
                'section_name' => 'Desserts',
                'products' => [
                    [
                        'name' => 'Tiramisu',
                        'description' => 'Classic Italian tiramisu',
                        'price' => 6.99,
                        'stock' => 10,
                        'status' => 'active',
                    ],
                    [
                        'name' => 'Fruit Tart',
                        'description' => 'Fresh fruit tart with custard',
                        'price' => 5.99,
                        'stock' => 8,
                        'status' => 'active',
                    ],
                ]
            ],
        ];

        foreach ($products as $categoryProducts) {
            $section = $sections->where('name', $categoryProducts['section_name'])->first();
            
            if ($section) {
                foreach ($categoryProducts['products'] as $product) {
                    $product['section_id'] = $section->id;
                    Product::create($product);
                }
            }
        }
        
        // Reset to default connection
        DB::setDefaultConnection('mysql');
    }
} 