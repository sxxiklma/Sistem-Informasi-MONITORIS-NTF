<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// Public mobile auth routes
Route::post('/login', [AuthController::class, 'login']);

// Protected mobile routes (Bearer Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // 1. Dashboard (All roles: Admin, Supervisor, Sales Motoris, Pimpinan)
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // 2. Visit Tasks (Tugas Kunjungan)
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::post('/tasks/{task}/respond', [TaskController::class, 'respond']);
    Route::post('/tasks/{task}/check-in', [TaskController::class, 'checkIn']);
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete']);

    // 3. Customers / Outlets (Pelanggan)
    Route::get('/customers', [CustomerApiController::class, 'index']);
    Route::post('/customers', [CustomerApiController::class, 'store']);
    Route::put('/customers/{customer}', [CustomerApiController::class, 'update']);

    // 4. Products / Catalog (Katalog Produk & Stok)
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::post('/products', [ProductApiController::class, 'store']);
    Route::put('/products/{product}', [ProductApiController::class, 'update']);

    // 5. Sales Orders (Penjualan & Pesanan)
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::post('/orders', [OrderApiController::class, 'store']);

    // 6. Reports & Target KPI (Laporan & Target)
    Route::get('/reports', [ReportApiController::class, 'index']);

    // 7. Users & Teams Management (Pengguna & Tim)
    Route::get('/users', [UserApiController::class, 'index']);
    Route::post('/users', [UserApiController::class, 'store']);

    // 8. Notifications (Notifikasi In-App)
    Route::get('/notifications/count', [NotificationApiController::class, 'count']);
    Route::get('/notifications', [NotificationApiController::class, 'index']);
    Route::post('/notifications/{notification}/read', [NotificationApiController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationApiController::class, 'markAllRead']);

    // 9. Profile & Password Management (Profil Akun)
    Route::post('/profile', [ProfileApiController::class, 'update']);
    Route::post('/profile/password', [ProfileApiController::class, 'updatePassword']);

    // 10. GPS Live Tracking & Maps (Live Tracking & Monitoring Peta)
    Route::post('/tracking/start', [TrackingController::class, 'startSession']);
    Route::post('/tracking/location', [TrackingController::class, 'sendLocation']);
    Route::post('/tracking/end', [TrackingController::class, 'endSession']);
    Route::get('/tracking/status', [TrackingController::class, 'sessionStatus']);
    Route::get('/tracking/live', [TrackingController::class, 'livePositions']);
    Route::get('/tracking/journey/{sessionId}', [TrackingController::class, 'journeyHistory']);
});
