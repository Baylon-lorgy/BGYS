<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
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
        return view('tenant.reports.index');
    }

    public function occupancy()
    {
        // Implementation for occupancy report
        return view('tenant.reports.occupancy');
    }

    public function revenue()
    {
        // Implementation for revenue report
        return view('tenant.reports.revenue');
    }

    public function generateProductsReportPdf()
    {
        $this->switchToTenantDatabase();
        $tenant = Auth::guard('tenant')->user();

        // Fetch products data with their sections
        $products = DB::connection('tenant')
            ->table('products')
            ->join('sections', 'products.section_id', '=', 'sections.id')
            ->select('products.*', 'sections.name as section_name')
            ->orderBy('sections.name')
            ->orderBy('products.name')
            ->get();

        // Get sales data for each product
        $salesData = DB::connection('tenant')
            ->table('order_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Load the view for the PDF report
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('tenant.reports.products_pdf', compact('products', 'tenant', 'salesData'));

        // Optional: Set paper size and orientation
        $pdf->setPaper('a4', 'landscape');

        // Download the PDF file
        return $pdf->download('products_report_' . Str::slug($tenant->name) . '.pdf');
    }
} 