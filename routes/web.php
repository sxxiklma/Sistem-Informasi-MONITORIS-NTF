<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VisitTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));
Route::get('/offline', fn () => view('offline'))->name('offline');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ─────────────────────────────────────────────────────────────────────────
    // CUSTOMERS & PRODUCTS (Katalog & Outlet: Akses lihat untuk semua role; Tambah/Edit/Hapus khusus kelola master data)
    // ─────────────────────────────────────────────────────────────────────────
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::middleware('permission:kelola master data')->group(function () {
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::patch('/customers/{customer}', [CustomerController::class, 'update']);
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        // Pengaturan Kategori & Jenis Pelanggan
        Route::get('/customer-types', [CustomerTypeController::class, 'index'])->name('customer-types.index');
        Route::post('/customer-types', [CustomerTypeController::class, 'store'])->name('customer-types.store');
        Route::put('/customer-types/{customerType}', [CustomerTypeController::class, 'update'])->name('customer-types.update');
        Route::patch('/customer-types/{customerType}', [CustomerTypeController::class, 'update']);
        Route::patch('/customer-types/{customerType}/toggle', [CustomerTypeController::class, 'toggleStatus'])->name('customer-types.toggle');
        Route::delete('/customer-types/{customerType}', [CustomerTypeController::class, 'destroy'])->name('customer-types.destroy');

        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // SALES ORDERS (kelola pesanan)
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('permission:kelola pesanan')->group(function () {
        Route::resource('orders', SalesOrderController::class)->only(['index', 'create', 'store']);
    });

    // ─────────────────────────────────────────────────────────────────────────
    // VISIT TASKS — new multi-status visit task management
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('permission:kelola kunjungan')->group(function () {
        Route::get('/visit-tasks', [VisitTaskController::class, 'index'])->name('visit-tasks.index');
        Route::post('/visit-tasks', [VisitTaskController::class, 'store'])->name('visit-tasks.store');
        Route::get('/visit-tasks/{visitTask}', [VisitTaskController::class, 'show'])->name('visit-tasks.show');
        Route::post('/visit-tasks/{visitTask}/accept', [VisitTaskController::class, 'accept'])->name('visit-tasks.accept');
        Route::post('/visit-tasks/{visitTask}/reject', [VisitTaskController::class, 'reject'])->name('visit-tasks.reject');
        Route::post('/visit-tasks/{visitTask}/reschedule', [VisitTaskController::class, 'reschedule'])
            ->name('visit-tasks.reschedule')
            ->middleware('role:Admin|Supervisor Sales');
        Route::post('/visit-tasks/{visitTask}/cancel', [VisitTaskController::class, 'cancel'])
            ->name('visit-tasks.cancel')
            ->middleware('role:Admin|Supervisor Sales');
        Route::post('/visit-tasks/{visitTask}/status', [VisitTaskController::class, 'updateStatus'])->name('visit-tasks.status');
        Route::post('/visit-tasks/{visitTask}/order', [VisitTaskController::class, 'storeOrder'])->name('visit-tasks.order');
        // API endpoint for live tracking map
        Route::get('/api/visit-tasks/active', [VisitTaskController::class, 'apiIndex'])->name('api.visit-tasks.active');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────────
    // VISITS & LIVE TRACKING (Rute, Navigasi Peta, & Live Tracking GPS terpadu)
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('permission:kelola kunjungan|lihat dashboard|lihat laporan')->group(function () {
        Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
        Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
        Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
        Route::put('/visits/{visit}', [VisitController::class, 'update'])->name('visits.update');
        Route::delete('/visits/{visit}', [VisitController::class, 'destroy'])->name('visits.destroy');
        Route::patch('/visits/{visit}/check-in', [VisitController::class, 'checkIn'])->name('visits.check-in');
        Route::patch('/visits/{visit}/check-out', [VisitController::class, 'checkOut'])->name('visits.check-out');
        Route::get('/api/visits/{visitTask}/journey', [VisitController::class, 'journey'])->name('api.visits.journey');
        Route::get('/api/map/visits', [MapController::class, 'visits'])->name('api.map.visits');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // GPS LIVE TRACKING (Merged into /visits, with redirect and APIs preserved)
    // ─────────────────────────────────────────────────────────────────────────
    Route::get('/tracking', fn () => redirect()->route('visits.index'))->name('tracking.index');

    // API endpoints — accessible to Sales Motoris on web
    Route::post('/api/tracking/start', [TrackingController::class, 'startSession'])->name('api.tracking.start');
    Route::post('/api/tracking/location', [TrackingController::class, 'sendLocation'])->name('api.tracking.location');
    Route::post('/api/tracking/end', [TrackingController::class, 'endSession'])->name('api.tracking.end');
    Route::get('/api/tracking/status', [TrackingController::class, 'sessionStatus'])->name('api.tracking.status');
    Route::get('/api/tracking/live', [TrackingController::class, 'livePositions'])->name('api.tracking.live');
    Route::get('/api/tracking/journey/{sessionId}', [TrackingController::class, 'journeyHistory'])->name('api.tracking.journey');

    // ─────────────────────────────────────────────────────────────────────────
    // IN-APP NOTIFICATIONS
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/count', [NotificationController::class, 'count'])->name('count');
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{appNotification}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // FINANCIAL & RECEIVABLES (Laporan Keuangan, Tempo, DP Kios & Pelunasan)
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('permission:lihat laporan|kelola pesanan')->group(function () {
        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/finance/orders/{order}/payments', [FinanceController::class, 'recordPayment'])->name('finance.payments.store');
        Route::get('/finance/orders/{order}/payments', [FinanceController::class, 'payments'])->name('finance.payments.index');
        Route::get('/finance/export/pdf', [FinanceController::class, 'exportPdf'])->name('finance.export.pdf');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // REPORTS, TARGETS, USERS, SETTINGS
    // ─────────────────────────────────────────────────────────────────────────
    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('permission:lihat laporan')
        ->name('modules.reports');

    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])
        ->middleware('permission:lihat laporan')
        ->name('reports.export.excel');

    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
        ->middleware('permission:lihat laporan')
        ->name('reports.export.pdf');

    Route::get('/reports/sales-orders.xlsx', [ReportController::class, 'salesOrders'])
        ->middleware('permission:lihat laporan')
        ->name('reports.sales-orders');

    Route::get('/targets', [ModuleController::class, 'targets'])
        ->middleware('permission:lihat laporan')
        ->name('modules.targets');

    Route::resource('users', UserManagementController::class)
        ->only(['index', 'create', 'store', 'update', 'destroy'])
        ->middleware('permission:kelola pengguna')
        ->names([
            'index' => 'modules.users',
            'create' => 'modules.users.create',
            'store' => 'modules.users.store',
            'update' => 'modules.users.update',
            'destroy' => 'modules.users.destroy',
        ]);

    Route::get('/settings', [ModuleController::class, 'settings'])
        ->middleware('permission:kelola pengaturan')
        ->name('modules.settings');

    // ─────────────────────────────────────────────────────────────────────────
    // PROFILE
    // ─────────────────────────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
