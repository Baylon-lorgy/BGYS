<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\TenantDashboardController;
use App\Http\Controllers\PublicRoomsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\RoomController;
use App\Http\Controllers\Tenant\BookingController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\ReportController;

Route::get('/', function () {
    return view('welcome');
});

// Public room browsing and booking routes
Route::prefix('rooms')->name('public.')->group(function () {
    Route::get('/', [PublicRoomsController::class, 'index'])->name('rooms.index');
    Route::get('/tenant/{tenantId}', [PublicRoomsController::class, 'tenantRooms'])->name('rooms.tenant');
    Route::get('/{tenantId}/{roomId}', [PublicRoomsController::class, 'show'])->name('rooms.show');
    Route::get('/{tenantId}/{roomId}/book', [PublicRoomsController::class, 'bookingForm'])->name('rooms.booking-form');
    Route::post('/{tenantId}/{roomId}/book', [PublicRoomsController::class, 'book'])->name('rooms.book');
});

// Public routes
Route::post('/tenant/register', [TenantController::class, 'register'])->name('tenant.register');

// Tenant authentication routes
Route::domain('{subdomain}.' . parse_url(config('app.url'), PHP_URL_HOST))->group(function () {
    Route::get('/login', [TenantAuthController::class, 'showLoginForm'])->name('tenant.login');
    Route::post('/login', [TenantAuthController::class, 'login'])->name('tenant.login.submit');
    Route::post('/logout', [TenantAuthController::class, 'logout'])->name('tenant.logout');
});

// Fallback routes for when no subdomain is present
Route::get('/tenant/login', [TenantAuthController::class, 'showLoginForm'])->name('tenant.login');
Route::post('/tenant/login', [TenantAuthController::class, 'login'])->name('tenant.login.submit');
Route::post('/tenant/logout', [TenantAuthController::class, 'logout'])->name('tenant.logout');

// Tenant dashboard routes
Route::middleware(['web', 'auth:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\TenantDashboardController::class, 'index'])->name('dashboard');

    // Rooms Management
    Route::resource('rooms', RoomController::class);

    // Bookings Management
    Route::resource('bookings', BookingController::class);
    Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');

    // Subscription routes
    Route::get('/subscription', [App\Http\Controllers\Tenant\SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/upgrade', [App\Http\Controllers\Tenant\SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::post('/subscription/downgrade', [App\Http\Controllers\Tenant\SubscriptionController::class, 'downgrade'])->name('subscription.downgrade');

    // New route for checking room number uniqueness
    Route::post('/rooms/checkRoomNumber', [App\Http\Controllers\Tenant\RoomController::class, 'checkRoomNumber'])->name('rooms.checkRoomNumber');

    // Route for generating rooms report PDF
    Route::get('/rooms/report/pdf', [App\Http\Controllers\Tenant\RoomController::class, 'generateRoomsReportPdf'])->name('rooms.report.pdf');

    // Products Management
    Route::resource('products', ProductController::class);
    
    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('products/report/pdf', [ReportController::class, 'generateProductsReportPdf'])->name('products.report.pdf');
});

// Admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tenant Management Routes
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::post('tenants/{tenant}/approve', [TenantController::class, 'approve'])->name('tenants.approve');
    Route::post('tenants/{tenant}/send-approval-email', [TenantController::class, 'sendApprovalEmail'])->name('tenants.send-approval-email');
    Route::post('tenants/{tenant}/reject', [TenantController::class, 'reject'])->name('tenants.reject');
    Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
    Route::delete('tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
});

require __DIR__.'/auth.php';

