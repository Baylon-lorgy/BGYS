<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Services\DatabaseManager;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create admin user
        User::create([
            'name' => 'MasterBaker Admin',
            'email' => 'admin@masterbaker.com',
            'password' => Hash::make('password123'),
        ]);

        // Create sample tenants
        $tenants = [
            [
                'name' => 'Sarah Dougherty',
                'bakery_name' => 'Sweet Treats Bakery',
                'email' => 'sarah@sweetreats.com',
                'domain_name' => 'sweettreats.localhost:8000',
                'contact_number' => '1234567890',
                'plan' => 'pro',
                'status' => 'active',
                'approved_at' => now(),
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Mike Butterworth',
                'bakery_name' => 'Cinnamon Spice Bakery',
                'email' => 'mike@cinnamonspice.com',
                'domain_name' => 'cinnamonspice.localhost:8000',
                'contact_number' => '0987654321',
                'plan' => 'free',
                'status' => 'pending',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Claire Croissant',
                'bakery_name' => 'French Pastry Haven',
                'email' => 'claire@frenchpastry.com',
                'domain_name' => 'frenchpastry.localhost:8000',
                'contact_number' => '5555555555',
                'plan' => 'pro',
                'status' => 'suspended',
                'suspended_at' => now(),
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($tenants as $tenantData) {
            $tenant = Tenant::create($tenantData);
            
            // If tenant is active, create their database and seed it
            if ($tenant->status === 'active') {
                try {
                    $databaseManager = app(DatabaseManager::class);
                    $databaseManager->createTenantDatabase($tenant, true);
                    
                    // Switch to tenant database connection
                    config(['database.default' => 'tenant']);
                    
                    // Seed the tenant's database with sections and products
                    $this->call([
                        SectionSeeder::class,
                        ProductSeeder::class,
                    ]);
                    
                    // Switch back to default connection
                    config(['database.default' => 'mysql']);
                } catch (\Exception $e) {
                    // Log error but continue seeding
                    \Log::error("Failed to create/seed database for tenant {$tenant->id}: " . $e->getMessage());
                }
            }
        }
    }
}
