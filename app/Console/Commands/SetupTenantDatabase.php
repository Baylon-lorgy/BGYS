<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupTenantDatabase extends Command
{
    protected $signature = 'tenant:setup {domain}';
    protected $description = 'Set up a tenant database with required tables';

    public function handle()
    {
        $domain = $this->argument('domain');
        
        // Find the tenant
        $tenant = Tenant::where('domain_name', $domain)->first();
        
        if (!$tenant) {
            $this->error("Tenant with domain {$domain} not found.");
            return 1;
        }

        // Store current database name
        $currentDatabase = Config::get('database.connections.mysql.database');

        try {
            // Switch to tenant database
            Config::set('database.connections.mysql.database', $tenant->database_name);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Check if sessions table exists
            if (!Schema::hasTable('sessions')) {
                $this->info("Creating sessions table for tenant {$domain}...");
                Schema::create('sessions', function ($table) {
                    $table->string('id')->primary();
                    $table->foreignId('user_id')->nullable()->index();
                    $table->string('ip_address', 45)->nullable();
                    $table->text('user_agent')->nullable();
                    $table->longText('payload');
                    $table->integer('last_activity')->index();
                });
                $this->info("Sessions table created successfully.");
            } else {
                $this->info("Sessions table already exists for tenant {$domain}.");
            }

            $this->info("Tenant database setup completed successfully.");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error setting up tenant database: " . $e->getMessage());
            return 1;
        } finally {
            // Switch back to main database
            Config::set('database.connections.mysql.database', $currentDatabase);
            DB::purge('mysql');
            DB::reconnect('mysql');
        }
    }
} 