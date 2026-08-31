<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReceiptScanController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Dompetify Finance App Backend
|--------------------------------------------------------------------------
*/

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'Dompetify REST API',
        'session_lifetime_minutes' => config('session.lifetime'),
        'sanctum_expiration_minutes' => config('sanctum.expiration'),
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Public Authentication Endpoints
Route::post('/register', [AuthController::class, 'apiRegister']);
Route::post('/login', [AuthController::class, 'apiLogin']);

// Android Notification Hook Webhook Endpoint (Protected by secret or public hook)
Route::post('/webhook/notification', [WebhookController::class, 'handleNotification']);

// Protected Endpoints (Requires valid Sanctum Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    // Current User Profile & Quick Stats
    Route::get('/me', [AuthController::class, 'apiMe']);
    Route::post('/logout', [AuthController::class, 'apiLogout']);

    // Wallets Endpoints
    Route::apiResource('wallets', WalletController::class);

    // Categories Endpoints
    Route::apiResource('categories', CategoryController::class);

    // Transactions Endpoints
    Route::apiResource('transactions', TransactionController::class)->except(['update']);

    // Vision AI Receipt Scan Endpoint
    Route::post('receipts/scan', [ReceiptScanController::class, 'scan']);
});
