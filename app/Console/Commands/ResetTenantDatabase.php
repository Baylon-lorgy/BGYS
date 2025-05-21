<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class ResetTenantDatabase extends Command
{
    protected $signature = 'tenant:reset {domain}';
    protected $description = 'Reset a tenant database by dropping and recreating it';

    public function handle()
    {
        $domain = $this->argument('domain');
        
        // Find the tenant
        $tenant = Tenant::where('domain_name', $domain)->first();
        
        if (!$tenant) {
            $this->error("Tenant with domain {$domain} not found.");
            return 1;
        }

        $database = $tenant->database_name;

        try {
            // Drop the database if it exists
            DB::statement("DROP DATABASE IF EXISTS `{$database}`");
            $this->info("Dropped database: {$database}");

            // Create the database
            DB::statement("CREATE DATABASE `{$database}`");
            $this->info("Created database: {$database}");

            // Configure the tenant connection
            Config::set('database.connections.tenant', [
                'driver' => 'mysql',
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'database' => $database,
                'username' => config('database.connections.mysql.username'),
                'password' => config('database.connections.mysql.password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);

            // Clear the config cache
            DB::purge('tenant');

            // Connect to the new database
            DB::connection('tenant')->getPdo();
            $this->info("Connected to database: {$database}");

            // Run migrations
            $this->info("Running migrations...");
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true
            ]);

            // Run seeders
            $this->info("Running seeders...");
            Artisan::call('db:seed', [
                '--class' => 'SectionSeeder',
                '--force' => true
            ]);
            Artisan::call('db:seed', [
                '--class' => 'ProductSeeder',
                '--force' => true
            ]);

            $this->info("Successfully reset database for tenant: {$tenant->name}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to reset database: " . $e->getMessage());
            return 1;
        }
    }
} 