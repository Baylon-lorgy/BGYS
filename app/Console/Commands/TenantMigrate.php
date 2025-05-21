<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class TenantMigrate extends Command
{
    protected $signature = 'tenant:migrate {tenant?} {--fresh : Drop all tables and re-run all migrations}';
    protected $description = 'Run migrations for tenant databases';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $fresh = $this->option('fresh');

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant not found!");
                return 1;
            }
            $this->migrateTenant($tenant, $fresh);
        } else {
            $tenants = Tenant::where('status', 'active')->get();
            foreach ($tenants as $tenant) {
                $this->migrateTenant($tenant, $fresh);
            }
        }

        return 0;
    }

    protected function migrateTenant($tenant, $fresh = false)
    {
        $this->info("Migrating tenant database: {$tenant->name} ({$tenant->database_name})");

        // Configure tenant database connection
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');

        if ($fresh) {
            $this->info("Dropping all tables for {$tenant->name}");
            
            // Disable foreign key checks
            DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Get all table names
            $tables = DB::connection('tenant')
                ->select('SHOW TABLES');

            // Drop each table
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                Schema::connection('tenant')->drop($tableName);
            }
            
            // Re-enable foreign key checks
            DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // Run migrations
        try {
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true
            ]);
            
            $this->info("Migration completed for {$tenant->name}");
            
            // Run seeders if fresh migration
            if ($fresh) {
                $this->info("Seeding data for {$tenant->name}");
                $this->call('db:seed', [
                    '--class' => 'SectionSeeder',
                    '--force' => true
                ]);
                $this->call('db:seed', [
                    '--class' => 'ProductSeeder',
                    '--force' => true
                ]);
            }
        } catch (\Exception $e) {
            $this->error("Migration failed for {$tenant->name}: " . $e->getMessage());
        }
    }
} 