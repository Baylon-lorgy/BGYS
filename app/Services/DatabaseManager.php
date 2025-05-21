<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DatabaseManager
{
    public function createTenantDatabase(Tenant $tenant, $fresh = true)
    {
        $database = $tenant->database_name;

        try {
            if ($fresh) {
                // Drop the database if it exists
                DB::statement("DROP DATABASE IF EXISTS `{$database}`");
                Log::info("Dropped existing database: {$database}");
            }
            
            // Create the database with proper escaping
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$database}`");
            Log::info("Created database: {$database}");

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

            // Clear the config cache and connect to the new database
            DB::purge('tenant');
            DB::connection('tenant')->getPdo();
            Log::info("Connected to database: {$database}");

            // Run migrations for the tenant database
            $this->runTenantMigrations();
            Log::info("Completed migrations for database: {$database}");

            // Create required tables if they don't exist
            $this->createRequiredTables();
            Log::info("Created required tables for database: {$database}");

            // Seed initial data
            $this->seedInitialData($tenant);
            Log::info("Seeded initial data for database: {$database}");

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to create database for tenant {$tenant->id}: " . $e->getMessage());
            throw $e;
        }
    }

    private function runTenantMigrations()
    {
        try {
            // Set the database connection to tenant
            Config::set('database.default', 'tenant');

            // Run the migrations
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true
            ]);

            Log::info("Migration output: " . Artisan::output());

            return true;
        } catch (\Exception $e) {
            Log::error("Migration failed: " . $e->getMessage());
            throw $e;
        } finally {
            // Reset the default connection
            Config::set('database.default', 'mysql');
        }
    }

    private function createRequiredTables()
    {
        $tables = [
            'products' => function ($table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->integer('stock')->default(0);
                $table->string('image_path')->nullable();
                $table->unsignedBigInteger('section_id');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
                $table->foreign('section_id')->references('id')->on('sections');
            },
            'sections' => function ($table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            },
            'sessions' => function ($table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            }
        ];

        foreach ($tables as $tableName => $callback) {
            if (!Schema::connection('tenant')->hasTable($tableName)) {
                Schema::connection('tenant')->create($tableName, $callback);
                Log::info("Created table: {$tableName}");
            }
        }
    }

    private function seedInitialData(Tenant $tenant)
    {
        // Only seed if the sections table is empty
        if (DB::connection('tenant')->table('sections')->count() === 0) {
            // Seed sections
            $sections = [
                [
                    'name' => 'Breads',
                    'description' => 'Fresh baked breads',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Pastries',
                    'description' => 'Delicious pastries',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Cakes',
                    'description' => 'Custom cakes',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ];

            DB::connection('tenant')->table('sections')->insert($sections);
            Log::info("Seeded sections table");

            // Seed sample products for each section
            $sections = DB::connection('tenant')->table('sections')->get();
            foreach ($sections as $section) {
                $products = [
                    [
                        'name' => 'Sample ' . $section->name . ' 1',
                        'description' => 'Sample product description',
                        'price' => 9.99,
                        'stock' => 10,
                        'section_id' => $section->id,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name' => 'Sample ' . $section->name . ' 2',
                        'description' => 'Sample product description',
                        'price' => 14.99,
                        'stock' => 15,
                        'section_id' => $section->id,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ];

                DB::connection('tenant')->table('products')->insert($products);
            }
            Log::info("Seeded products table");
        }
    }

    public function deleteTenantDatabase(Tenant $tenant)
    {
        try {
            DB::statement("DROP DATABASE IF EXISTS `{$tenant->database_name}`");
            Log::info("Deleted database: {$tenant->database_name}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to delete database for tenant {$tenant->id}: " . $e->getMessage());
            throw $e;
        }
    }
} 