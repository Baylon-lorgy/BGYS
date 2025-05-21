<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Tenant;

class TenantDatabaseSeeder extends Seeder
{
    public function run()
    {
        // Get the first tenant (for testing purposes)
        $tenant = Tenant::first();
        
        if (!$tenant) {
            throw new \Exception('No tenant found to seed data for.');
        }

        // Configure tenant database connection
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Clear existing data
        DB::connection('tenant')->table('products')->delete();
        DB::connection('tenant')->table('sections')->delete();

        // Seed sections
        $sections = [
            [
                'tenant_id' => $tenant->id,
                'name' => 'Breads',
                'description' => 'Fresh baked breads',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Pastries',
                'description' => 'Delicious pastries',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Cakes',
                'description' => 'Custom cakes',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('sections')->insert($sections);

        // Get section IDs
        $breadSection = DB::connection('tenant')->table('sections')->where('name', 'Breads')->first()->id;
        $pastriesSection = DB::connection('tenant')->table('sections')->where('name', 'Pastries')->first()->id;
        $cakesSection = DB::connection('tenant')->table('sections')->where('name', 'Cakes')->first()->id;

        // Seed products
        $products = [
            [
                'name' => 'Sourdough Bread',
                'description' => 'Traditional sourdough bread with crispy crust',
                'price' => 5.99,
                'section_id' => $breadSection,
                'stock' => 20,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Croissant',
                'description' => 'Buttery and flaky croissant',
                'price' => 3.99,
                'section_id' => $pastriesSection,
                'stock' => 30,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chocolate Cake',
                'description' => 'Rich chocolate cake with ganache',
                'price' => 25.99,
                'section_id' => $cakesSection,
                'stock' => 5,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('products')->insert($products);
    }
} 