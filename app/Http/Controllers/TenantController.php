<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\TenantApproved;
use App\Mail\TenantRejected;
use App\Notifications\TenantRegistrationNotification;

class TenantController extends Controller
{
    protected $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    public function index()
    {
        $tenants = Tenant::latest()->paginate(10);
        return view('tenants.index', compact('tenants'));
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'bakery_name' => 'required|string|max:255',
                'email' => 'required|email|unique:tenants',
                'domain_name' => 'required|string|unique:tenants',
                'contact_number' => 'required|string|max:20',
                'plan' => 'required|in:free,pro',
            ]);

            // Ensure domain_name ends with .localhost:8000
            $subdomain = $validated['domain_name'];
            if (!str_ends_with($subdomain, '.localhost:8000')) {
                $subdomain .= '.localhost:8000';
            }

            // Set a temporary password that will be replaced when tenant is approved
            $temporaryPassword = Hash::make('temp_' . Str::random(8));

            $tenant = Tenant::create([
                'name' => $validated['name'],
                'bakery_name' => $validated['bakery_name'],
                'email' => $validated['email'],
                'domain_name' => $subdomain,
                'contact_number' => $validated['contact_number'],
                'plan' => $validated['plan'],
                'status' => 'pending',
                'password' => $temporaryPassword,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully. Waiting for approval.',
                'tenant' => $tenant
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Tenant registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bakery_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'domain_name' => 'required|string|unique:tenants,domain_name,' . $tenant->id,
            'contact_number' => 'required|string|max:20',
            'plan' => 'required|in:free,pro',
            'status' => 'required|in:pending,active,suspended',
        ]);

        // Update the tenant with the validated data
        $tenant->update($validated);

        // If status is changed to active, set approved_at
        if ($validated['status'] === 'active' && $tenant->status !== 'active') {
            $tenant->update([
                'approved_at' => now(),
                'suspended_at' => null,
            ]);
        }

        // If status is changed to suspended, set suspended_at
        if ($validated['status'] === 'suspended' && $tenant->status !== 'suspended') {
            $tenant->update([
                'suspended_at' => now(),
                'approved_at' => null,
            ]);
        }

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        try {
            // Delete tenant's database
            $this->databaseManager->deleteTenantDatabase($tenant);

            // Delete tenant record
            $tenant->delete();

            return redirect()->back()->with('success', 'Tenant and associated database deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Tenant deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete tenant. Please try again.');
        }
    }

    public function approve(Tenant $tenant)
    {
        try {
            // Generate a temporary password
            $temporaryPassword = Str::random(12);
            
            // Update tenant with new password and status
            $tenant->update([
                'status' => 'active',
                'approved_at' => now(),
                'password' => Hash::make($temporaryPassword)
            ]);

            // Create database for tenant
            $this->databaseManager->createTenantDatabase($tenant);

            // Send approval notification with the temporary password
            $tenant->notify(new TenantRegistrationNotification($tenant, $temporaryPassword));

            return redirect()->back()->with('success', 'Tenant approved successfully and database created.');
        } catch (\Exception $e) {
            \Log::error('Tenant approval failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve tenant. Please try again.');
        }
    }

    public function sendApprovalEmail(Request $request, Tenant $tenant)
    {
        try {
            $password = $request->input('password');
            
            // Send approval email
            Mail::to($tenant->email)->send(new TenantApproved($tenant, $password));

            return redirect()->route('dashboard')
                ->with('success', 'Approval email has been sent to the tenant.');
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Failed to send approval email. Please check the logs for details.');
        }
    }

    /**
     * Run migrations for a tenant's database
     */
    private function runTenantMigrations($databaseName)
    {
        // Store the current database name
        $currentDatabase = config('database.connections.mysql.database');
        
        // Switch to the tenant's database
        config(['database.connections.mysql.database' => $databaseName]);
        DB::purge('mysql');
        
        try {
            // Run the tenant schema migration
            \Artisan::call('migrate', [
                '--path' => 'database/migrations/2024_03_23_000000_create_tenant_schema.php',
                '--database' => 'mysql',
                '--force' => true,
            ]);

            // Create sessions table
            DB::statement('CREATE TABLE IF NOT EXISTS `sessions` (
                `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `user_id` bigint unsigned DEFAULT NULL,
                `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `user_agent` text COLLATE utf8mb4_unicode_ci,
                `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                `last_activity` int NOT NULL,
                PRIMARY KEY (`id`),
                KEY `sessions_user_id_index` (`user_id`),
                KEY `sessions_last_activity_index` (`last_activity`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        } finally {
            // Switch back to the original database
            config(['database.connections.mysql.database' => $currentDatabase]);
            DB::purge('mysql');
        }
    }

    public function suspend(Tenant $tenant)
    {
        try {
            $tenant->update([
                'status' => 'suspended',
                'suspended_at' => now()
            ]);

            return redirect()->back()->with('success', 'Tenant suspended successfully.');
        } catch (\Exception $e) {
            \Log::error('Tenant suspension failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to suspend tenant. Please try again.');
        }
    }

    public function reject(Request $request, Tenant $tenant)
    {
        try {
            $validated = $request->validate([
                'reason' => 'nullable|string|max:1000',
            ]);

            // Update tenant status to rejected
            $tenant->update([
                'status' => 'rejected',
                'approved_at' => null,
                'suspended_at' => null,
            ]);

            // Send rejection email
            Mail::to($tenant->email)->send(new TenantRejected($tenant, $validated['reason'] ?? null));

            return redirect()->route('dashboard')
                ->with('success', 'Tenant has been rejected and notified via email.');
        } catch (\Exception $e) {
            \Log::error('Failed to reject tenant: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Failed to reject tenant. Please check the logs for details.');
        }
    }

    public function activate(Tenant $tenant)
    {
        $tenant->status = 'active';
        $tenant->save();

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant activated successfully.');
    }
} 