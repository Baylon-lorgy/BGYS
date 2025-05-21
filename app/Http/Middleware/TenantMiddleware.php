<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return redirect()->route('tenant.login')->with('error', 'Please login to access the tenant dashboard.');
            }

            // Get the authenticated user
            $user = Auth::user();

            // Check if the user is a tenant
            if (!$user instanceof Tenant) {
                Auth::logout();
                return redirect()->route('tenant.login')->with('error', 'Access denied. Please login as a tenant.');
            }

            // Store current database name
            $currentDatabase = Config::get('database.connections.mysql.database');

            try {
                // Configure tenant database connection
                Config::set('database.connections.tenant', [
                    'driver' => 'mysql',
                    'host' => config('database.connections.mysql.host'),
                    'port' => config('database.connections.mysql.port'),
                    'database' => $user->database_name,
                    'username' => config('database.connections.mysql.username'),
                    'password' => config('database.connections.mysql.password'),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ]);

                // Clear previous connection and reconnect
                DB::purge('tenant');
                DB::reconnect('tenant');

                // Verify connection and required tables
                DB::connection('tenant')->getPdo();
                
                // Store tenant information in session
                session([
                    'tenant_id' => $user->id,
                    'tenant_database' => $user->database_name,
                    'main_database' => $currentDatabase
                ]);

                // Block access if tenant is not active
                if ($user->status !== 'active') {
                    Auth::logout();
                    return redirect()->route('tenant.login')->with('error', 'This tenant account is not active.');
                }

                // Set tenant connection as default for this request
                Config::set('database.default', 'tenant');

                // Proceed with the request
                $response = $next($request);

                return $response;

            } catch (\Exception $e) {
                Log::error('Tenant middleware error: ' . $e->getMessage(), [
                    'tenant_id' => $user->id,
                    'database_name' => $user->database_name,
                    'error' => $e->getMessage()
                ]);

                Auth::logout();
                return redirect()->route('tenant.login')
                    ->with('error', 'Unable to access your account. Please contact support if this problem persists.');
            } finally {
                // Always switch back to main database after the request
                Config::set('database.connections.mysql.database', $currentDatabase);
                DB::purge('mysql');
                DB::reconnect('mysql');
            }
        } catch (\Exception $e) {
            Log::error('Tenant middleware critical error: ' . $e->getMessage());
            return redirect()->route('tenant.login')
                ->with('error', 'An error occurred. Please try again or contact support.');
        }
    }
} 