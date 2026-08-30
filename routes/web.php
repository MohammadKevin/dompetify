<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'Dompetify API',
        'status' => 'online',
        'version' => '1.0.0',
        'endpoints' => [
            'wallets' => '/api/wallets',
            'categories' => '/api/categories',
            'transactions' => '/api/transactions',
            'scan_receipt' => '/api/receipts/scan',
        ],
    ]);
});

Route::get('/install', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = Artisan::output();

        Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = Artisan::output();

        Artisan::call('storage:link');
        $storageOutput = Artisan::output();

        return response("
            <div style='font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 30px; border: 1px solid #10B981; border-radius: 12px; background: #F0FDF4;'>
                <h2 style='color: #065F46;'>✅ Database & Storage Berhasil Di-Setup!</h2>
                <p style='color: #047857;'>Seluruh tabel, data dompet awal (BCA, BRImo, GoPay, OVO, DANA), kategori, dan storage symlink sudah siap digunakan di cPanel.</p>
                <hr style='border: 0; border-top: 1px solid #A7F3D0; margin: 20px 0;'>
                <pre style='background: #064E3B; color: #6EE7B7; padding: 15px; border-radius: 8px; font-size: 12px; overflow-x: auto;'>{$migrateOutput}\n{$seedOutput}\n{$storageOutput}</pre>
                <br>
                <a href='/' style='display: inline-block; background: #10B981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold;'>Buka API</a>
            </div>
        ");
    } catch (\Throwable $e) {
        return response("
            <div style='font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 30px; border: 1px solid #EF4444; border-radius: 12px; background: #FEF2F2;'>
                <h2 style='color: #991B1B;'>❌ Gagal Setup Database</h2>
                <p style='color: #B91C1C;'>Error: {$e->getMessage()}</p>
                <p style='color: #7F1D1D; font-size: 13px;'>Pastikan database <code>finance4_finance_db</code> dan user <code>finance4_finance_user</code> sudah dibuat dan dihubungkan di cPanel MySQL Databases.</p>
            </div>
        ", 500);
    }
});
