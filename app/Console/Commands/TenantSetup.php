<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Tenant;

class TenantSetup extends Command
{
    protected $signature = 'tenant:setup {tenant_id}';
    protected $description = 'Set up a tenant\'s database and run migrations';

    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant not found!");
            return 1;
        }

        $this->info("Setting up database for tenant: {$tenant->name}");

        try {
            // Create database if it doesn't exist
            DB::statement("CREATE DATABASE IF NOT EXISTS {$tenant->database_name}");
            
            // Configure tenant database connection
            config([
                'database.connections.tenant.database' => $tenant->database_name
            ]);

            // Clear previous connection and reconnect
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Check if tables exist
            $hasTables = Schema::connection('tenant')->hasTable('sections') && 
                        Schema::connection('tenant')->hasTable('products');

            if (!$hasTables) {
                // Run migrations only if tables don't exist
                $this->info('Running migrations...');
                $this->call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant',
                    '--force' => true
                ]);
            } else {
                $this->info('Tables already exist, skipping migrations...');
            }

            // Check if tables are empty
            $sectionsCount = DB::connection('tenant')->table('sections')->count();
            $productsCount = DB::connection('tenant')->table('products')->count();

            if ($sectionsCount === 0 || $productsCount === 0) {
                // Run seeder only if tables are empty
                $this->info('Tables are empty, running seeder...');
                $this->call('db:seed', [
                    '--class' => 'TenantDatabaseSeeder',
                    '--force' => true
                ]);
            } else {
                $this->info('Tables already have data, skipping seeder...');
                $this->info("Current counts - Sections: $sectionsCount, Products: $productsCount");
            }

            $this->info('Tenant setup completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error("Error setting up tenant database: " . $e->getMessage());
            return 1;
        }
    }
} 