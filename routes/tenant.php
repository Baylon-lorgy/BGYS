<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\ReportController;
use App\Http\Controllers\Tenant\ProfileController;
use App\Http\Controllers\Tenant\SettingController;
use App\Http\Controllers\Tenant\SubscriptionController;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

// Products Management
Route::resource('products', ProductController::class)->names([
    'index' => 'tenant.products.index',
    'create' => 'tenant.products.create',
    'store' => 'tenant.products.store',
    'show' => 'tenant.products.show',
    'edit' => 'tenant.products.edit',
    'update' => 'tenant.products.update',
    'destroy' => 'tenant.products.destroy',
]);

// Reports
Route::get('reports', [ReportController::class, 'index'])->name('tenant.reports.index');
Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('tenant.reports.revenue');
Route::get('products/report/pdf', [ReportController::class, 'generateProductsReportPdf'])->name('tenant.products.report.pdf');

// Profile
Route::get('profile', [ProfileController::class, 'edit'])->name('tenant.profile.edit');
Route::patch('profile', [ProfileController::class, 'update'])->name('tenant.profile.update');
Route::delete('profile', [ProfileController::class, 'destroy'])->name('tenant.profile.destroy');

// Settings
Route::get('settings', [SettingController::class, 'index'])->name('tenant.settings.index');
Route::patch('settings', [SettingController::class, 'update'])->name('tenant.settings.update');

// Subscription
Route::get('subscription', [SubscriptionController::class, 'index'])->name('tenant.subscription.index');
Route::post('subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('tenant.subscription.upgrade');
Route::post('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('tenant.subscription.cancel'); 