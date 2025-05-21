<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    private function switchToTenantDatabase()
    {
        if (Auth::guard('tenant')->check()) {
            $tenant = Auth::guard('tenant')->user();
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }
    }

    public function index()
    {
        $tenant = Auth::guard('tenant')->user();
        
        // Get product count from tenant database
        $this->switchToTenantDatabase();
        $productCount = DB::connection('tenant')
            ->table('products')
            ->count();
            
        $maxProducts = $tenant->plan === 'pro' ? PHP_INT_MAX : 3;
        $canAddMoreProducts = $productCount < $maxProducts;

        // Get plan features
        $plans = [
            'free' => [
                'name' => 'Free Plan',
                'price' => '0',
                'features' => [
                    'Up to 3 products',
                    'Basic analytics',
                    'Email support'
                ]
            ],
            'pro' => [
                'name' => 'Pro Plan',
                'price' => '999',
                'features' => [
                    'Unlimited products',
                    'Advanced analytics',
                    'Priority support',
                    'Custom reports',
                    'Sales forecasting',
                    'Inventory alerts'
                ]
            ]
        ];
        
        return view('tenant.subscription.index', compact('tenant', 'productCount', 'maxProducts', 'canAddMoreProducts', 'plans'));
    }

    public function upgrade(Request $request)
    {
        $tenant = Auth::guard('tenant')->user();
        
        if ($tenant->plan === 'pro') {
            return redirect()->route('tenant.subscription.index')
                ->with('error', 'You are already on the Pro plan.');
        }

        // Here you would typically integrate with a payment gateway
        // For now, we'll just update the plan
        $tenant->update(['plan' => 'pro']);

        return redirect()->route('tenant.subscription.index')
            ->with('success', 'Successfully upgraded to Pro plan!');
    }

    public function cancel(Request $request)
    {
        $tenant = Auth::guard('tenant')->user();
        
        if ($tenant->plan === 'free') {
            return redirect()->route('tenant.subscription.index')
                ->with('error', 'You are already on the Free plan.');
        }

        // Check if downgrading is possible (product count within free plan limits)
        $this->switchToTenantDatabase();
        $productCount = DB::connection('tenant')
            ->table('products')
            ->count();
            
        if ($productCount > 3) {
            return redirect()->route('tenant.subscription.index')
                ->with('error', 'Cannot downgrade to Free plan. Please reduce your products to 3 or less first.');
        }

        $tenant->update(['plan' => 'free']);

        return redirect()->route('tenant.subscription.index')
            ->with('success', 'Successfully downgraded to Free plan.');
    }
} 