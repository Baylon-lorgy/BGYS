<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class MigrateTenantDatabase extends Command
{
    protected $signature = 'tenant:migrate {tenant_id?} {--fresh}';
    protected $description = 'Run migrations for tenant database(s)';

    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $fresh = $this->option('fresh');

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant not found!");
                return;
            }
            $this->migrateTenantDatabase($tenant, $fresh);
        } else {
            $tenants = Tenant::all();
            foreach ($tenants as $tenant) {
                $this->migrateTenantDatabase($tenant, $fresh);
            }
        }
    }

    protected function migrateTenantDatabase($tenant, $fresh = false)
    {
        $this->info("Migrating database for tenant: {$tenant->name}");

        // Configure the tenant's database connection
        config([
            'database.connections.tenant' => [
                'driver' => 'mysql',
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'database' => $tenant->database_name,
                'username' => config('database.connections.mysql.username'),
                'password' => config('database.connections.mysql.password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]
        ]);

        // Manually drop existing tables if fresh option is set
        if ($fresh) {
            $this->info("Dropping existing tables for tenant: {$tenant->name}");
            DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Drop all tables in the tenant's database
            $tables = DB::connection('tenant')->select('SHOW TABLES');
            foreach ($tables as $table) {
                $tableName = reset($table);
                DB::connection('tenant')->statement("DROP TABLE IF EXISTS `$tableName`");
            }
            
            DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=1');
            
            // Clear the migrations table
            DB::connection('tenant')->statement('DROP TABLE IF EXISTS migrations');
        }

        // Run migrations
        $this->call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true
        ]);

        $this->info("Completed migration for tenant: {$tenant->name}");
    }
} 