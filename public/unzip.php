<?php
/**
 * Dompetify Super-Fast Deployment Extractor
 * Automatically unpacks release.zip uploaded via GitHub Actions.
 */

// 1. Set execution limits for extraction
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');

header('Content-Type: application/json');

// 2. Security Check: Secret Token
$expectedSecret = getenv('DEPLOY_SECRET') ?: 'dompetify-secret-key-2026';
$providedSecret = $_GET['secret'] ?? '';

if (empty($providedSecret) || !hash_equals($expectedSecret, $providedSecret)) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Forbidden: Invalid or missing deployment secret.'
    ]);
    exit;
}

// 3. Define Paths
$rootPath = dirname(__DIR__);
$zipFile = $rootPath . '/release.zip';

if (!file_exists($zipFile)) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'release.zip not found in root directory.'
    ]);
    exit;
}

// 4. Extract ZipArchive
$startTime = microtime(true);
$zip = new ZipArchive();

if ($zip->open($zipFile) === TRUE) {
    // Extract all files to root
    $zip->extractTo($rootPath);
    $zip->close();

    // Delete zip after successful extraction
    @unlink($zipFile);

    // 5. Ensure Storage Directories & Permissions
    $directories = [
        $rootPath . '/storage/app/public',
        $rootPath . '/storage/framework/cache/data',
        $rootPath . '/storage/framework/sessions',
        $rootPath . '/storage/framework/views',
        $rootPath . '/storage/logs',
        $rootPath . '/bootstrap/cache',
    ];

    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
    }

    $duration = round(microtime(true) - $startTime, 2);

    echo json_encode([
        'status' => 'success',
        'message' => "Laravel backend successfully deployed & extracted in {$duration}s!",
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to open and extract release.zip.'
    ]);
}
