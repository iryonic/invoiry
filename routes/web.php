<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SuperAdmin\BrochureController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Public & Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('superadmin.index');
        }

        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/demo/{type}', [AuthController::class, 'demoLogin'])->name('demo.login');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Public Legal & Help Documentation Pages
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/help', [PageController::class, 'help'])->name('help');
Route::get('/documentation', [PageController::class, 'help'])->name('docs');
Route::get('/features-pricing-brochure.pdf', [BrochureController::class, 'publicDownload'])->name('brochure.public-download');

/*
|--------------------------------------------------------------------------
| Authenticated Tenant & Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // User Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.destroy');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/sessions/{sessionId}', [ProfileController::class, 'destroySession'])->name('profile.sessions.destroy');
    Route::post('/profile/sessions/logout-others', [ProfileController::class, 'logoutOtherSessions'])->name('profile.sessions.logout-others');
    Route::post('/profile/resend-verification', [ProfileController::class, 'resendVerification'])->name('profile.resend-verification');

    // Super Admin Routes (Protected by role:super_admin)
    Route::middleware(['role:super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/', [SuperAdminController::class, 'index'])->name('index');

        // Tenants Directory & Lifecycle Management
        Route::get('/tenants', [SuperAdminController::class, 'tenantsIndex'])->name('tenants.index');
        Route::post('/tenants/bulk', [SuperAdminController::class, 'bulkTenantsAction'])->name('tenants.bulk');
        Route::get('/tenants/create', [SuperAdminController::class, 'createTenant'])->name('tenants.create');
        Route::post('/tenants', [SuperAdminController::class, 'storeTenant'])->name('tenants.store');
        Route::get('/tenants/{tenant}', [SuperAdminController::class, 'showTenant'])->name('tenants.show');
        Route::get('/tenants/{tenant}/edit', [SuperAdminController::class, 'editTenant'])->name('tenants.edit');
        Route::put('/tenants/{tenant}', [SuperAdminController::class, 'updateTenant'])->name('tenants.update');
        Route::delete('/tenants/{tenant}', [SuperAdminController::class, 'destroyTenant'])->name('tenants.destroy');
        Route::post('/tenants/{tenant}/status', [SuperAdminController::class, 'toggleStatus'])->name('tenants.status');
        Route::post('/tenants/{tenant}/extend', [SuperAdminController::class, 'extendSubscription'])->name('tenants.extend');
        Route::get('/tenants/{tenant}/permissions', [SuperAdminController::class, 'permissionsIndex'])->name('tenants.permissions');
        Route::put('/tenants/{tenant}/permissions', [SuperAdminController::class, 'permissionsUpdate'])->name('tenants.permissions.update');

        // Impersonation
        Route::get('/impersonate/{tenant}', [SuperAdminController::class, 'impersonate'])->name('impersonate');
        Route::get('/leave-impersonate', [SuperAdminController::class, 'leaveImpersonate'])->name('leave-impersonate');

        // Global Users Management
        Route::get('/users', [SuperAdminController::class, 'usersIndex'])->name('users.index');
        Route::post('/users/bulk', [SuperAdminController::class, 'bulkUsersAction'])->name('users.bulk');
        Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [SuperAdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [SuperAdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [SuperAdminController::class, 'destroyUser'])->name('users.destroy');
        Route::post('/users/{user}/status', [SuperAdminController::class, 'toggleUserStatus'])->name('users.status');
        Route::get('/users/{user}/permissions', [SuperAdminController::class, 'userPermissionsIndex'])->name('users.permissions');
        Route::put('/users/{user}/permissions', [SuperAdminController::class, 'userPermissionsUpdate'])->name('users.permissions.update');

        // System Audit Logs
        Route::get('/audit-logs', [SuperAdminController::class, 'auditLogsIndex'])->name('audit-logs.index');

        // Platform System Settings
        Route::get('/settings', [SuperAdminController::class, 'settingsIndex'])->name('settings.index');
        Route::put('/settings', [SuperAdminController::class, 'settingsUpdate'])->name('settings.update');

        // Dynamic Features & Pricing Brochure Management
        Route::get('/brochure', [BrochureController::class, 'index'])->name('brochure.index');
        Route::post('/brochure/settings', [BrochureController::class, 'updateSettings'])->name('brochure.settings.update');
        Route::post('/brochure/modules', [BrochureController::class, 'storeModule'])->name('brochure.modules.store');
        Route::put('/brochure/modules/{module}', [BrochureController::class, 'updateModule'])->name('brochure.modules.update');
        Route::delete('/brochure/modules/{module}', [BrochureController::class, 'destroyModule'])->name('brochure.modules.destroy');
        Route::post('/brochure/modules/reorder', [BrochureController::class, 'reorderModules'])->name('brochure.modules.reorder');
        Route::post('/brochure/screenshots', [BrochureController::class, 'storeScreenshot'])->name('brochure.screenshots.store');
        Route::put('/brochure/screenshots/{screenshot}', [BrochureController::class, 'updateScreenshot'])->name('brochure.screenshots.update');
        Route::delete('/brochure/screenshots/{screenshot}', [BrochureController::class, 'destroyScreenshot'])->name('brochure.screenshots.destroy');
        Route::post('/brochure/plans', [BrochureController::class, 'storePlan'])->name('brochure.plans.store');
        Route::put('/brochure/plans/{plan}', [BrochureController::class, 'updatePlan'])->name('brochure.plans.update');
        Route::delete('/brochure/plans/{plan}', [BrochureController::class, 'destroyPlan'])->name('brochure.plans.destroy');
        Route::get('/brochure/preview', [BrochureController::class, 'preview'])->name('brochure.preview');
        Route::post('/brochure/generate', [BrochureController::class, 'generate'])->name('brochure.generate');
        Route::get('/brochure/download', [BrochureController::class, 'download'])->name('brochure.download');
    });

    // Tenant App Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');

    // Inward Inventory / Brought Items
    Route::get('/items', [ItemController::class, 'index'])->name('items.index')->middleware('permission:items.view');
    Route::post('/items/bulk', [ItemController::class, 'bulkAction'])->name('items.bulk')->middleware('permission:items.view');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create')->middleware('permission:items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store')->middleware('permission:items.create');
    Route::post('/items/quick-store', [ItemController::class, 'quickStore'])->name('items.quick-store')->middleware('permission:items.create');
    Route::get('/items/search-api', [ItemController::class, 'searchApi'])->name('items.search-api')->middleware('permission:items.view');
    Route::get('/api/items/search', [ItemController::class, 'searchApi'])->name('api.items.search')->middleware('permission:items.view');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show')->middleware('permission:items.view');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit')->middleware('permission:items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update')->middleware('permission:items.edit');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy')->middleware('permission:items.delete');

    // Dedicated Device Stock Management Ledger
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index')->middleware('permission:items.view');
    Route::post('/stock/{item}/adjust', [StockController::class, 'adjust'])->name('stock.adjust')->middleware('permission:items.edit');
    Route::get('/stock/{item}/history', [StockController::class, 'history'])->name('stock.history')->middleware('permission:items.view');

    // Invoices / Sell Items
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index')->middleware('permission:invoices.view');
    Route::post('/invoices/bulk', [InvoiceController::class, 'bulkAction'])->name('invoices.bulk')->middleware('permission:invoices.view');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create')->middleware('permission:invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store')->middleware('permission:invoices.create');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show')->middleware('permission:invoices.view');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit')->middleware('permission:invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update')->middleware('permission:invoices.edit');
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy')->middleware('permission:invoices.delete');

    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf')->middleware('permission:invoices.print');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print')->middleware('permission:invoices.print');
    Route::get('/invoices/{invoice}/thermal', [InvoiceController::class, 'thermal'])->name('invoices.thermal')->middleware('permission:invoices.print');
    Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment')->middleware('permission:invoices.record_payments');
    Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'recordPayment'])->name('invoices.payments.store')->middleware('permission:invoices.record_payments');

    // Customers & Khata
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('permission:customers.view');
    Route::post('/customers/bulk', [CustomerController::class, 'bulkAction'])->name('customers.bulk')->middleware('permission:customers.view');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('permission:customers.create');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show')->middleware('permission:customers.view');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('permission:customers.edit');
    Route::post('/customers/{customer}/settle', [CustomerController::class, 'recordKhataPayment'])->name('customers.settle')->middleware('permission:customers.edit');

    // Suppliers
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index')->middleware('permission:suppliers.view');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('permission:suppliers.create');

    // Store Management & Settings (Restricted from cashier by role:tenant_admin,super_admin)
    Route::middleware(['role:tenant_admin,super_admin'])->group(function () {
        // Staff & Role Management
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index')->middleware('permission:staff.view');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store')->middleware('permission:staff.create');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update')->middleware('permission:staff.edit');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy')->middleware('permission:staff.delete');
        Route::post('/staff/{staff}/status', [StaffController::class, 'toggleStatus'])->name('staff.status')->middleware('permission:staff.edit');

        // Store Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index')->middleware('permission:settings.view');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('permission:settings.manage');
        Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');
        Route::post('/settings/logout-other-devices', [AuthController::class, 'logoutOtherDevices'])->name('settings.logout-other-devices');
    });

    // POS Terminal Lock Screen & Quick Unlock
    Route::get('/lock', [AuthController::class, 'lock'])->name('lock');
    Route::post('/unlock', [AuthController::class, 'unlock'])->name('unlock');
});

// Public Storage Fallback for environments where symlinks are restricted or blocked
Route::get('/storage/{path}', function (string $path) {
    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return Storage::disk('public')->response($path);
})->where('path', '.*')->name('storage.fallback');
