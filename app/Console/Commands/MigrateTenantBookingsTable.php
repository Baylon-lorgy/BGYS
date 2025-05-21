<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MigrateTenantBookingsTable extends Command
{
    protected $signature = 'tenant:migrate-bookings {tenant : The name or ID of the tenant}';
    protected $description = 'Create the bookings table for a specific tenant';

    public function handle()
    {
        $tenantIdentifier = $this->argument('tenant');
        
        // Find the tenant by name or ID
        $tenant = is_numeric($tenantIdentifier) 
            ? Tenant::find($tenantIdentifier) 
            : Tenant::where('name', 'like', '%' . $tenantIdentifier . '%')->first();
        
        if (!$tenant) {
            $this->error("Tenant not found: {$tenantIdentifier}");
            return 1;
        }
        
        $this->info("Setting up bookings table for tenant: {$tenant->name}");
        
        // Configure and connect to the tenant database
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        // Check if the table already exists
        if (Schema::connection('tenant')->hasTable('bookings')) {
            $this->info("Bookings table already exists for this tenant. Dropping and recreating...");
            Schema::connection('tenant')->drop('bookings');
        }
        
        // Create the bookings table
        $this->info("Creating bookings table...");
        Schema::connection('tenant')->create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('guests');
            $table->text('special_requests')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        
        $this->info("Bookings table created successfully for tenant: {$tenant->name}");
        
        return 0;
    }
} 