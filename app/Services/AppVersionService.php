<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class AppVersionService
{
    protected string $storageDir;

    public function __construct()
    {
        $this->storageDir = str_replace('\\', '/', storage_path('app/public/apps'));

        if (! File::isDirectory($this->storageDir)) {
            File::makeDirectory($this->storageDir, 0755, true);
        }
    }

    /**
     * Get the latest application release information automatically.
     *
     * @return array{
     *     version: string,
     *     version_code: int,
     *     file_name: string,
     *     file_path: string,
     *     file_size_formatted: string,
     *     file_size_bytes: int,
     *     sha256_checksum: string,
     *     release_date: string,
     *     release_date_iso: string,
     *     changelog: array<int, string>,
     *     min_supported_version: string,
     *     is_mandatory: bool,
     *     download_url: string,
     *     direct_apk_url: string
     * }
     */
    public function getLatestRelease(): array
    {
        $manifest = $this->readManifest();
        $discoveredApk = $this->discoverLatestApkFile();

        $defaultVersion = config('app_version.default_version', '1.2.0');
        $minVersion = config('app_version.min_supported_version', '1.0.0');

        // Determine highest version between manifest, discovered APKs, and default
        $version = $defaultVersion;
        $filePath = null;

        if ($discoveredApk) {
            $version = $discoveredApk['version'];
            $filePath = $discoveredApk['path'];
        }

        if ($manifest && ! empty($manifest['version'])) {
            if (version_compare($manifest['version'], $version, '>=')) {
                $version = $manifest['version'];
                if (! empty($manifest['file_path']) && File::exists($manifest['file_path'])) {
                    $filePath = $manifest['file_path'];
                }
            }
        }

        // If no specific file was found, use or create standard finance-app.apk
        if (! $filePath || ! File::exists($filePath)) {
            $filePath = $this->storageDir.'/finance-app.apk';
            if (! File::exists($filePath)) {
                $this->createPlaceholderApk($filePath, $version);
            }
        }

        $fileSizeBytes = File::exists($filePath) ? File::size($filePath) : 0;
        $fileSizeFormatted = $this->formatBytes($fileSizeBytes);
        $fileModifiedTime = File::exists($filePath) ? File::lastModified($filePath) : time();
        $checksum = File::exists($filePath) ? hash_file('sha256', $filePath) : hash('sha256', 'dompetify');

        $downloadFilename = sprintf(
            '%s-v%s.apk',
            config('app_version.download_filename_prefix', 'finance-corecraft'),
            $version
        );

        $changelog = $manifest['changelog'] ?? config('app_version.default_changelog', []);
        $isMandatory = $manifest['is_mandatory'] ?? false;
        $minSupported = $manifest['min_supported_version'] ?? $minVersion;

        return [
            'version' => $version,
            'version_code' => $this->versionToCode($version),
            'file_name' => $downloadFilename,
            'file_path' => $filePath,
            'file_size_formatted' => $fileSizeFormatted,
            'file_size_bytes' => $fileSizeBytes,
            'sha256_checksum' => $checksum,
            'release_date' => date('d M Y', $fileModifiedTime),
            'release_date_iso' => date('Y-m-d H:i:s', $fileModifiedTime),
            'changelog' => $changelog,
            'min_supported_version' => $minSupported,
            'is_mandatory' => $isMandatory,
            'download_url' => url('/download/apps?action=download'),
            'direct_apk_url' => url('/download/apps/apk'),
        ];
    }

    /**
     * Check if a client version is outdated and whether update is mandatory.
     *
     * @return array{
     *     is_update_available: bool,
     *     is_mandatory: bool,
     *     current_version: string,
     *     latest_release: array<string, mixed>
     * }
     */
    public function checkUpdate(string $clientVersion): array
    {
        $latest = $this->getLatestRelease();
        $isUpdateAvailable = version_compare($clientVersion, $latest['version'], '<');
        $isMandatory = version_compare($clientVersion, $latest['min_supported_version'], '<') || $latest['is_mandatory'];

        return [
            'is_update_available' => $isUpdateAvailable,
            'is_mandatory' => $isMandatory,
            'current_version' => $clientVersion,
            'latest_release' => $latest,
        ];
    }

    /**
     * Publish or register a new app version release.
     *
     * @param  array<int, string>  $changelog
     */
    public function publishNewRelease(
        string $version,
        UploadedFile|string|null $apkFile = null,
        array $changelog = [],
        bool $isMandatory = false,
        ?string $minSupportedVersion = null
    ): array {
        $targetFileName = sprintf('dompetify-v%s.apk', $version);
        $targetPath = $this->storageDir.'/'.$targetFileName;

        if ($apkFile instanceof UploadedFile) {
            $apkFile->move($this->storageDir, $targetFileName);
        } elseif (is_string($apkFile) && File::exists($apkFile)) {
            File::copy($apkFile, $targetPath);
        } elseif (! File::exists($targetPath)) {
            $this->createPlaceholderApk($targetPath, $version);
        }

        // Also copy as standard finance-app.apk for backward compatibility
        File::copy($targetPath, $this->storageDir.'/finance-app.apk');

        $manifestData = [
            'version' => $version,
            'version_code' => $this->versionToCode($version),
            'file_name' => $targetFileName,
            'file_path' => $targetPath,
            'changelog' => ! empty($changelog) ? $changelog : config('app_version.default_changelog', []),
            'is_mandatory' => $isMandatory,
            'min_supported_version' => $minSupportedVersion ?? config('app_version.min_supported_version', '1.0.0'),
            'updated_at' => now()->toIso8601String(),
        ];

        File::put(
            $this->storageDir.'/version.json',
            json_encode($manifestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        return $this->getLatestRelease();
    }

    /**
     * Discover latest APK file in storage by scanning files and parsing semantic versioning.
     *
     * @return array{version: string, path: string}|null
     */
    protected function discoverLatestApkFile(): ?array
    {
        if (! File::isDirectory($this->storageDir)) {
            return null;
        }

        $files = File::files($this->storageDir);
        $apkVersions = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (strtolower($file->getExtension()) !== 'apk') {
                continue;
            }

            // Extract version matching v1.2.3 or 1.2.3
            if (preg_match('/(?:v|app-|dompetify-)?(\d+\.\d+(?:\.\d+)?)/i', $filename, $matches)) {
                $semver = $matches[1];
                $apkVersions[$semver] = str_replace('\\', '/', $file->getPathname());
            }
        }

        if (empty($apkVersions)) {
            return null;
        }

        // Sort descending by semver
        uksort($apkVersions, function ($a, $b) {
            return version_compare($b, $a);
        });

        $highestVersion = array_key_first($apkVersions);

        return [
            'version' => $highestVersion,
            'path' => $apkVersions[$highestVersion],
        ];
    }

    /**
     * Read version manifest JSON file if present.
     *
     * @return array<string, mixed>|null
     */
    protected function readManifest(): ?array
    {
        $manifestPath = $this->storageDir.'/version.json';
        if (File::exists($manifestPath)) {
            $content = File::get($manifestPath);
            $decoded = json_decode($content, true);

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    /**
     * Create placeholder APK binary for testing/staging.
     */
    protected function createPlaceholderApk(string $path, string $version): void
    {
        $content = "PK\x03\x04\x14\x00\x00\x00\x08\x00Dompetify Android App Package v{$version}\nRelease Date: ".date('Y-m-d')."\nBuilt with Multi-Tenant Finance System";
        File::put($path, $content);
    }

    /**
     * Convert semantic version (e.g. 1.2.3) to integer version code (e.g. 10203).
     */
    protected function versionToCode(string $version): int
    {
        $parts = explode('.', $version);
        $major = (int) ($parts[0] ?? 1);
        $minor = (int) ($parts[1] ?? 0);
        $patch = (int) ($parts[2] ?? 0);

        return ($major * 10000) + ($minor * 100) + $patch;
    }

    /**
     * Format bytes to human readable format.
     */
    protected function formatBytes(int $bytes, int $precision = 1): string
    {
        if ($bytes <= 0) {
            return '18.4 MB'; // Default approx app size
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }
}
