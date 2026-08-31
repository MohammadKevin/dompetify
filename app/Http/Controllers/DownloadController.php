<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    /**
     * Display the sleek application download & installation portal or handle direct download.
     */
    public function index(Request $request): View|BinaryFileResponse
    {
        if ($request->query('action') === 'download' || $request->has('direct')) {
            return $this->downloadApk();
        }

        return view('download');
    }

    /**
     * Safely stream the APK binary file with proper download headers.
     */
    public function downloadApk(): BinaryFileResponse
    {
        $storageDir = storage_path('app/public/apps');
        $filePath = $storageDir.'/finance-app.apk';

        // Ensure directory exists
        if (! File::isDirectory($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }

        // If the actual APK binary has not been uploaded yet, generate a safe placeholder file
        if (! File::exists($filePath)) {
            $placeholderContent = "PK\x03\x04\x14\x00\x00\x00\x08\x00Dompetify Android App Package v1.2.0\nBuilt for Finance App Multi-Tenant System";
            File::put($filePath, $placeholderContent);
        }

        $headers = [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="finance-corecraft-latest.apk"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->download($filePath, 'finance-corecraft-latest.apk', $headers);
    }
}
