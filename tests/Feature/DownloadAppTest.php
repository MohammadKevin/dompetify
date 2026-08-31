<?php

namespace Tests\Feature;

use Tests\TestCase;

class DownloadAppTest extends TestCase
{
    public function test_download_apps_page_renders_with_installation_guide(): void
    {
        $response = $this->get('/download/apps');

        $response->assertStatus(200)
            ->assertSee('Dompetify Mobile')
            ->assertSee('Android Standalone APK')
            ->assertSee('Petunjuk Instalasi APK Android')
            ->assertSee('Izinkan Sumber Tidak Dikenal')
            ->assertSee('finance-corecraft-latest.apk');
    }

    public function test_direct_apk_download_serves_binary_with_correct_headers(): void
    {
        $response = $this->get('/download/apps/apk');

        $response->assertStatus(200)
            ->assertHeader('content-disposition', 'attachment; filename=finance-corecraft-latest.apk')
            ->assertHeader('content-type', 'application/vnd.android.package-archive');
    }

    public function test_query_param_action_download_serves_binary(): void
    {
        $response = $this->get('/download/apps?action=download');

        $response->assertStatus(200)
            ->assertHeader('content-disposition', 'attachment; filename=finance-corecraft-latest.apk');
    }
}
