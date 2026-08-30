<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReceiptScanController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - FinanceApp Backend
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'FinanceApp REST API',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Wallets Endpoints
Route::apiResource('wallets', WalletController::class);

// Categories Endpoints
Route::apiResource('categories', CategoryController::class);

// Transactions Endpoints
Route::apiResource('transactions', TransactionController::class)->except(['update']);

// Vision AI Receipt Scan Endpoint
Route::post('receipts/scan', [ReceiptScanController::class, 'scan']);

// Android Notification Hook Webhook Endpoint
Route::post('webhook/notification', [WebhookController::class, 'handleNotification']);
