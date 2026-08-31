<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mobile Application Release Metadata
    |--------------------------------------------------------------------------
    |
    | When no specific APK file with a semver name or version.json is found in
    | storage/app/public/apps, this fallback configuration will be utilized.
    |
    */

    'app_name' => env('APP_NAME', 'Dompetify'),

    'default_version' => env('APP_LATEST_VERSION', '1.2.0'),

    'min_supported_version' => env('APP_MIN_SUPPORTED_VERSION', '1.0.0'),

    'package_name' => env('APP_PACKAGE_NAME', 'com.dompetify.financeapp'),

    'download_filename_prefix' => env('APP_DOWNLOAD_PREFIX', 'finance-corecraft'),

    'default_changelog' => [
        'Multi-account wallet management (BCA, BRImo, GoPay, DANA, Cash)',
        'Sesi login persistent 90 hari (3 bulan)',
        'AI Vision Smart Receipt Scan OCR',
        'Auto-provisioning 5 starter wallets untuk akun baru',
        'Perbaikan performa dan keamanan row-level multi-tenant',
    ],

];
