<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class TenantAuthController extends Controller
{
    public function showLoginForm()
    {
        // Store the main database name in session
        session(['main_database' => Config::get('database.connections.mysql.database')]);
        
        // Get the subdomain from the request
        $subdomain = $this->getSubdomain();
        
        // If subdomain exists, pre-fill the domain_name field
        if ($subdomain) {
            return view('auth.tenant-login', ['domain_name' => $subdomain]);
        }
        
        return view('auth.tenant-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'domain_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login attempt for domain: ' . $credentials['domain_name'] . ', email: ' . $credentials['email']);

        // Make sure we're using the main database
        $mainDatabase = session('main_database', 'final_project');
        Config::set('database.connections.mysql.database', $mainDatabase);
        DB::purge('mysql');
        DB::reconnect('mysql');

        try {
            $tenant = Tenant::where('domain_name', $credentials['domain_name'])
                           ->where('email', $credentials['email'])
                           ->first();

            if (!$tenant) {
                Log::warning('Tenant not found for domain: ' . $credentials['domain_name'] . ', email: ' . $credentials['email']);
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
            }

            Log::info('Tenant found. Status: ' . $tenant->status);

            if (!Hash::check($credentials['password'], $tenant->password)) {
                Log::warning('Password mismatch for tenant: ' . $tenant->email);
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
            }

            if ($tenant->status !== 'active') {
                Log::warning('Tenant not active. Status: ' . $tenant->status);
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact the administrator.',
                ]);
            }

            // Store tenant data in session
            session([
                'tenant_id' => $tenant->id,
                'tenant_database' => $tenant->database_name,
                'main_database' => $mainDatabase
            ]);

            // Login the tenant using the tenant guard
            Auth::guard('tenant')->login($tenant, $request->has('remember'));
            Log::info('Successfully logged in tenant: ' . $tenant->email);

            // Redirect to tenant dashboard with success message
            return redirect()->route('tenant.dashboard')->with('success', 'Welcome back, ' . $tenant->name . '!');
        } catch (\Exception $e) {
            Log::error('Login error for tenant ' . ($tenant->email ?? 'unknown') . ': ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ]);
        }
    }

    public function logout(Request $request)
    {
        // Switch back to main database before logout
        if ($mainDatabase = session('main_database')) {
            Config::set('database.connections.mysql.database', $mainDatabase);
            DB::purge('mysql');
            DB::reconnect('mysql');
        }

        Auth::guard('tenant')->logout();
        $request->session()->forget(['tenant_id', 'tenant_database', 'main_database']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function getSubdomain()
    {
        $host = request()->getHost();
        $parts = explode('.', $host);
        
        // If we have more than 2 parts and the first part is not 'www'
        if (count($parts) > 2 && $parts[0] !== 'www') {
            return $parts[0];
        }
        
        return null;
    }
} 