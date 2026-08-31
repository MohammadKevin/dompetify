<?php

namespace Tests\Feature;

use App\Services\AppVersionService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AppVersionTest extends TestCase
{
    protected string $storageDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storageDir = str_replace('\\', '/', storage_path('app/public/apps'));
    }

    public function test_can_retrieve_default_latest_release_metadata(): void
    {
        $service = app(AppVersionService::class);
        $release = $service->getLatestRelease();

        $this->assertNotEmpty($release['version']);
        $this->assertNotEmpty($release['file_path']);
        $this->assertNotEmpty($release['sha256_checksum']);
        $this->assertNotEmpty($release['download_url']);
        $this->assertFileExists($release['file_path']);
    }

    public function test_auto_detects_higher_version_apk_file(): void
    {
        $newApkPath = $this->storageDir.'/dompetify-v2.0.1.apk';
        File::put($newApkPath, "PK\x03\x04\x14\x00\x00\x00\x08\x00Dompetify v2.0.1 Test Binary");

        try {
            $service = app(AppVersionService::class);
            $release = $service->getLatestRelease();

            $this->assertEquals('2.0.1', $release['version']);
            $this->assertEquals($newApkPath, $release['file_path']);
            $this->assertEquals(hash_file('sha256', $newApkPath), $release['sha256_checksum']);
        } finally {
            if (File::exists($newApkPath)) {
                File::delete($newApkPath);
            }
        }
    }

    public function test_api_returns_latest_release_json(): void
    {
        $response = $this->getJson('/api/app/latest-release');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'version',
                    'version_code',
                    'file_name',
                    'file_size_formatted',
                    'sha256_checksum',
                    'release_date',
                    'changelog',
                    'download_url',
                ],
            ]);
    }

    public function test_api_checks_if_client_is_outdated(): void
    {
        $response = $this->getJson('/api/app/latest-release?current_version=1.0.0');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.is_update_available', true)
            ->assertJsonPath('data.current_version', '1.0.0');
    }

    public function test_publish_new_release_updates_manifest(): void
    {
        $service = app(AppVersionService::class);

        $changelog = ['Fitur baru transfer QRIS', 'Perbaikan bug session'];
        $release = $service->publishNewRelease('1.5.0', null, $changelog, false, '1.2.0');

        $this->assertEquals('1.5.0', $release['version']);
        $this->assertEquals($changelog, $release['changelog']);

        // Clean up test release
        $v150Path = $this->storageDir.'/dompetify-v1.5.0.apk';
        $manifestPath = $this->storageDir.'/version.json';
        if (File::exists($v150Path)) {
            File::delete($v150Path);
        }
        if (File::exists($manifestPath)) {
            File::delete($manifestPath);
        }
    }
}
