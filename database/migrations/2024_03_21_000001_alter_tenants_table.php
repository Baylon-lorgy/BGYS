<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('tenants', 'owner_name')) {
                $table->dropColumn('owner_name');
            }
            if (Schema::hasColumn('tenants', 'owner_email')) {
                $table->dropColumn('owner_email');
            }
            if (Schema::hasColumn('tenants', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('tenants', 'address')) {
                $table->dropColumn('address');
            }
            
            // Add new columns if they don't exist
            if (!Schema::hasColumn('tenants', 'boarding_house_name')) {
                $table->string('boarding_house_name')->after('name');
            }
            if (!Schema::hasColumn('tenants', 'email')) {
                $table->string('email')->unique()->after('boarding_house_name');
            }
            if (!Schema::hasColumn('tenants', 'domain_name')) {
                $table->string('domain_name')->unique()->after('email');
            }
            if (!Schema::hasColumn('tenants', 'contact_number')) {
                $table->string('contact_number')->after('domain_name');
            }
            if (!Schema::hasColumn('tenants', 'plan')) {
                $table->enum('plan', ['free', 'pro'])->default('free')->after('contact_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Re-add old columns
            $table->string('owner_name')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            
            // Drop new columns if they exist
            if (Schema::hasColumn('tenants', 'boarding_house_name')) {
                $table->dropColumn('boarding_house_name');
            }
            if (Schema::hasColumn('tenants', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('tenants', 'domain_name')) {
                $table->dropColumn('domain_name');
            }
            if (Schema::hasColumn('tenants', 'contact_number')) {
                $table->dropColumn('contact_number');
            }
            if (Schema::hasColumn('tenants', 'plan')) {
                $table->dropColumn('plan');
            }
        });
    }
}; 