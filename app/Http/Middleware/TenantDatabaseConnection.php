<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class TenantDatabaseConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        // Get domain from request
        $domain = $request->getHost();

        // Find tenant by domain
        $tenant = Tenant::where('domain_name', $domain)->first();

        if ($tenant) {
            // Configure and connect to tenant database
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Set as default connection for this request
            Config::set('database.default', 'tenant');
        }

        return $next($request);
    }
} 