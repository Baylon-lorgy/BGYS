<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class TenantDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tenant');
    }

    private function switchToTenantDatabase()
    {
        if (Auth::guard('tenant')->check()) {
            $tenant = Auth::guard('tenant')->user();
            
            // Log tenant information
            \Log::info('Switching to tenant database:', [
                'tenant_id' => $tenant->id,
                'database_name' => $tenant->database_name,
                'name' => $tenant->name
            ]);

            // Configure tenant database connection
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Verify connection
            try {
                DB::connection('tenant')->getPdo();
                \Log::info('Successfully connected to tenant database');

                // Verify required tables exist
                $requiredTables = ['products', 'sections'];
                $missingTables = [];

                foreach ($requiredTables as $table) {
                    if (!Schema::connection('tenant')->hasTable($table)) {
                        $missingTables[] = $table;
                    }
                }

                if (!empty($missingTables)) {
                    \Log::error('Missing required tables in tenant database:', [
                        'missing_tables' => $missingTables,
                        'tenant_id' => $tenant->id,
                        'database_name' => $tenant->database_name
                    ]);

                    // Initialize missing tables using DatabaseManager
                    $databaseManager = app(\App\Services\DatabaseManager::class);
                    $databaseManager->createTenantDatabase($tenant, false);
                }

                // Set as default connection for this request
                Config::set('database.default', 'tenant');
            } catch (\Exception $e) {
                \Log::error('Failed to connect to tenant database:', [
                    'error' => $e->getMessage(),
                    'database' => $tenant->database_name
                ]);
                throw $e;
            }
        }
    }

    public function index()
    {
        $tenant = Auth::guard('tenant')->user();

        try {
            $this->switchToTenantDatabase();

            // Log database connection details
            \Log::info('Tenant Database Connection:', [
                'tenant_id' => $tenant->id,
                'database_name' => $tenant->database_name,
                'connection' => DB::connection('tenant')->getName(),
                'database' => DB::connection('tenant')->getDatabaseName()
            ]);

            // Get product statistics with proper stock conditions
            $productStats = [
                'total' => DB::connection('tenant')->table('products')->count(),
                'in_stock' => DB::connection('tenant')->table('products')
                    ->where('stock', '>', 10)
                    ->count(),
                'best_sellers' => DB::connection('tenant')
                    ->table('products')
                    ->where('stock', '>', 0)
                    ->orderBy('stock', 'desc')
                    ->limit(5)
                    ->count(),
                'low_stock' => DB::connection('tenant')
                    ->table('products')
                    ->where('stock', '<=', 10)
                    ->where('stock', '>', 0)
                    ->count()
            ];

            // Log product statistics for debugging
            \Log::info('Product Statistics:', $productStats);

            // Get sections with their products
            $sections = DB::connection('tenant')
                ->table('sections')
                ->where('status', 'active')
                ->get();

            // Get recent products with pagination
            $products = DB::connection('tenant')
                ->table('products')
                ->join('sections', 'products.section_id', '=', 'sections.id')
                ->select(
                    'products.*',
                    'sections.name as section_name'
                )
                ->orderBy('products.created_at', 'desc')
                ->paginate(10);

            // Structure dashboard data
            $dashboardData = [
                'tenant' => $tenant,
                'product_stats' => $productStats,
                'sections' => $sections,
                'plan' => $tenant->plan,
                'bakery_name' => $tenant->bakery_name
            ];

            // Add pro features if tenant has pro plan
            if ($tenant->plan === 'pro') {
                $dashboardData['pro_features'] = [
                    'revenue_chart' => $this->getRevenueChartData(),
                    'sales_summary' => $this->getMonthlySummary(),
                ];
            }

            return view('tenant.dashboard', [
                'dashboardData' => $dashboardData,
                'products' => $products
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in tenant dashboard:', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
                'database_name' => $tenant->database_name
            ]);

            // Return view with empty data and error message
            return view('tenant.dashboard', [
                'dashboardData' => [
                    'tenant' => $tenant,
                    'product_stats' => [
                        'total' => 0,
                        'in_stock' => 0,
                        'best_sellers' => 0,
                        'low_stock' => 0
                    ],
                    'sections' => collect([]),
                    'plan' => $tenant->plan,
                    'bakery_name' => $tenant->bakery_name,
                    'error' => 'Error loading dashboard data. Please try refreshing the page or contact support if the problem persists.'
                ],
                'products' => collect([])
            ]);
        }
    }

    private function getDashboardData($tenant)
    {
        $data = [
            'plan' => $tenant->plan,
            'boarding_house_name' => $tenant->boarding_house_name,
        ];

        // Get room statistics
        $data['room_stats'] = [
            'total' => DB::table('rooms')->count(),
            'available' => DB::table('rooms')->where('status', 'available')->count(),
            'occupied' => DB::table('rooms')->where('status', 'occupied')->count(),
            'maintenance' => DB::table('rooms')->where('status', 'maintenance')->count(),
        ];

        // Get booking statistics
        $data['booking_stats'] = [
            'total' => DB::table('bookings')->count(),
            'pending' => DB::table('bookings')->where('status', 'pending')->count(),
            'confirmed' => DB::table('bookings')->where('status', 'confirmed')->count(),
            'completed' => DB::table('bookings')->where('status', 'completed')->count(),
        ];

        // Get payment statistics
        $data['payment_stats'] = [
            'total_revenue' => DB::table('payments')->where('status', 'completed')->sum('amount'),
            'pending_payments' => DB::table('payments')->where('status', 'pending')->sum('amount'),
        ];

        // Get recent bookings
        $data['recent_bookings'] = DB::table('bookings')
            ->join('room_tenants', 'bookings.tenant_id', '=', 'room_tenants.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'room_tenants.name as tenant_name', 'rooms.room_number')
            ->orderBy('bookings.created_at', 'desc')
            ->limit(5)
            ->get();

        // Get maintenance requests
        $data['maintenance_requests'] = DB::table('maintenance_requests')
            ->join('rooms', 'maintenance_requests.room_id', '=', 'rooms.id')
            ->select('maintenance_requests.*', 'rooms.room_number')
            ->orderBy('maintenance_requests.created_at', 'desc')
            ->limit(5)
            ->get();

        // Add pro features if tenant has pro plan
        if ($tenant->plan === 'pro') {
            $data['pro_features'] = [
                'revenue_chart' => $this->getRevenueChartData(),
                'occupancy_rate' => $this->calculateOccupancyRate(),
                'monthly_summary' => $this->getMonthlySummary(),
            ];
        }

        return $data;
    }

    private function getRevenueChartData()
    {
        // Get last 6 months revenue data
        return DB::connection('tenant')
            ->table('payments')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->get();
    }

    private function calculateOccupancyRate()
    {
        $totalRooms = DB::connection('tenant')->table('rooms')->count();
        $occupiedRooms = DB::connection('tenant')->table('rooms')->where('status', 'occupied')->count();
        
        return $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;
    }

    private function getMonthlySummary()
    {
        $currentMonth = now()->format('Y-m');
        
        return [
            'bookings' => DB::connection('tenant')->table('bookings')
                ->where('created_at', 'like', $currentMonth . '%')
                ->count(),
            'revenue' => DB::connection('tenant')->table('payments')
                ->where('status', 'completed')
                ->where('created_at', 'like', $currentMonth . '%')
                ->sum('amount'),
        ];
    }

    private function getRoomsChartData()
    {
        // Get rooms count per week for the last 12 weeks
        return DB::connection('tenant')
            ->table('rooms')
            ->where('created_at', '>=', now()->subWeeks(12))
            ->select(DB::raw('YEARWEEK(created_at) as week'), DB::raw('COUNT(*) as total'))
            ->groupBy('week')
            ->orderBy('week')
            ->get();
    }

    private function getBookingsChartData()
    {
        // Get bookings count per week for the last 12 weeks
        return DB::connection('tenant')
            ->table('bookings')
            ->where('created_at', '>=', now()->subWeeks(12))
            ->select(DB::raw('YEARWEEK(created_at) as week'), DB::raw('COUNT(*) as total'))
            ->groupBy('week')
            ->orderBy('week')
            ->get();
    }

    private function getSalesChartData()
    {
        // Get sales data for the last 12 weeks
        return DB::connection('tenant')
            ->table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', now()->subWeeks(12))
            ->select(
                DB::raw('YEARWEEK(orders.created_at) as week'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('week')
            ->orderBy('week')
            ->get();
    }

    private function getWeeklyReportData()
    {
        // Get the last 12 weeks
        $weeks = collect();
        for ($i = 11; $i >= 0; $i--) {
            $weeks->push(now()->subWeeks($i)->format('Y-W'));
        }

        // Get sales data
        $salesData = $this->getSalesChartData();

        // Format data for chart
        return $weeks->mapWithKeys(function ($week) use ($salesData) {
            $weekData = $salesData->firstWhere('week', $week);
            return [
                $week => [
                    'sales' => $weekData ? $weekData->total_sales : 0,
                    'orders' => $weekData ? $weekData->order_count : 0,
                ]
            ];
        });
    }

    public function createRoom()
    {
        return view('tenant.rooms.create');
    }

    public function storeRoom(Request $request)
    {
        try {
            $tenant = Auth::guard('tenant')->user();
            
            // Configure the tenant's database connection
            config([
                'database.connections.tenant' => [
                    'driver' => 'mysql',
                    'host' => config('database.connections.mysql.host'),
                    'port' => config('database.connections.mysql.port'),
                    'database' => $tenant->database_name,
                    'username' => config('database.connections.mysql.username'),
                    'password' => config('database.connections.mysql.password'),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ]
            ]);
            
            // Set the connection for validation
            $room = new Room();
            $room->setConnection('tenant');
            
            $validated = $request->validate([
                'room_number' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($room) {
                        if ($room->where('room_number', $value)->exists()) {
                            $fail('This room number is already taken.');
                        }
                    },
                ],
                'floor' => 'required|string',
                'capacity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'nullable|string',
            ]);

            $validated['tenant_id'] = $tenant->id;
            $validated['status'] = 'available';

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $photo->storeAs('public/rooms', $filename);
                $validated['photo'] = 'rooms/' . $filename;
            }

            $room->fill($validated);
            $room->save();

            return redirect()->route('tenant.dashboard')->with('success', 'Room added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding room: ' . $e->getMessage())->withInput();
        }
    }
} 