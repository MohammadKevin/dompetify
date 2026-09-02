<?php

namespace App\Http\Controllers;

use App\Services\AppVersionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    public function __construct(
        protected AppVersionService $appVersionService
    ) {}

    /**
     * Display the application download & installation portal with dynamic version metadata.
     * Auto-triggers APK download on Android mobile browsers.
     */
    public function index(Request $request): View|BinaryFileResponse
    {
        if ($request->query('action') === 'download' || $request->has('direct')) {
            return $this->downloadApk();
        }

        $release = $this->appVersionService->getLatestRelease();
        $userAgent = $request->userAgent() ?? '';
        $isAndroid = stripos($userAgent, 'Android') !== false;
        $isMobile = $isAndroid || (bool) preg_match('/Mobile|webOS|iPhone|iPad|iPod|Opera Mini/i', $userAgent);

        return view('download', compact('release', 'isAndroid', 'isMobile'));
    }

    /**
     * Safely stream the latest APK binary file with dynamic headers and version tracking.
     */
    public function downloadApk(): BinaryFileResponse
    {
        $release = $this->appVersionService->getLatestRelease();
        $filePath = $release['file_path'];

        $headers = [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename=finance-corecraft-latest.apk',
            'X-App-Version' => $release['version'],
            'X-App-Version-Code' => (string) $release['version_code'],
            'X-App-SHA256' => $release['sha256_checksum'],
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->download($filePath, 'finance-corecraft-latest.apk', $headers);
    }

    /**
     * API endpoint for mobile clients to check latest release and auto-update metadata.
     */
    public function apiLatestRelease(Request $request): JsonResponse
    {
        $clientVersion = $request->query('current_version');

        if ($clientVersion) {
            $check = $this->appVersionService->checkUpdate($clientVersion);

            return response()->json([
                'success' => true,
                'data' => $check,
            ]);
        }

        $release = $this->appVersionService->getLatestRelease();

        return response()->json([
            'success' => true,
            'data' => $release,
        ]);
    }
}
