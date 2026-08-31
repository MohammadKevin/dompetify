<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DownloadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Dompetify Finance App
|--------------------------------------------------------------------------
*/

// Public Landing Page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Dedicated App Download Portal & Binary Stream
Route::get('/download/apps', [DownloadController::class, 'index'])->name('download.apps');
Route::get('/download/apps/apk', [DownloadController::class, 'downloadApk'])->name('download.apk');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Web Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();
        $wallets = $user->wallets()->orderBy('is_active', 'desc')->orderBy('name', 'asc')->get();
        $recentTransactions = $user->transactions()->with(['wallet', 'category'])->orderBy('date', 'desc')->take(10)->get();
        $totalNetWorth = (float) $wallets->where('is_active', true)->sum('balance');

        return view('dashboard', compact('user', 'wallets', 'recentTransactions', 'totalNetWorth'));
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// cPanel / Production One-Click Database Setup Utility
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
                <p style='color: #047857;'>Seluruh tabel, data starter, dan storage symlink sudah siap digunakan.</p>
                <hr style='border: 0; border-top: 1px solid #A7F3D0; margin: 20px 0;'>
                <pre style='background: #064E3B; color: #6EE7B7; padding: 15px; border-radius: 8px; font-size: 12px; overflow-x: auto;'>{$migrateOutput}\n{$seedOutput}\n{$storageOutput}</pre>
                <br>
                <a href='/' style='display: inline-block; background: #10B981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold;'>Buka Landing Page</a>
            </div>
        ");
    } catch (Throwable $e) {
        return response("
            <div style='font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 30px; border: 1px solid #EF4444; border-radius: 12px; background: #FEF2F2;'>
                <h2 style='color: #991B1B;'>❌ Gagal Setup Database</h2>
                <p style='color: #B91C1C;'>Error: {$e->getMessage()}</p>
            </div>
        ", 500);
    }
});
