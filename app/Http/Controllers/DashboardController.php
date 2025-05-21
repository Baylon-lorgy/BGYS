<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tenants = Tenant::latest()->paginate(10);
        $totalTenants = Tenant::count();
        $pendingTenants = Tenant::where('status', 'pending')->count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $suspendedTenants = Tenant::where('status', 'suspended')->count();

        return view('dashboard', compact(
            'tenants',
            'totalTenants',
            'pendingTenants',
            'activeTenants',
            'suspendedTenants'
        ));
    }
} 