<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReceiptScanApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_receipt_scan_endpoint_validation(): void
    {
        $response = $this->postJson('/api/receipts/scan', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    public function test_receipt_scan_with_fallback_or_mock_gemini_api(): void
    {
        Storage::fake('public');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'merchant_name' => 'Indomaret Point',
                                        'transaction_date' => '2026-08-30 14:20:00',
                                        'total_amount' => 54000.00,
                                        'suggested_category' => 'Makanan & Minuman',
                                        'items' => [
                                            ['item_name' => 'Kopi Point Caramel', 'quantity' => 1, 'price' => 25000.00],
                                            ['item_name' => 'Sandwich Tuna', 'quantity' => 1, 'price' => 29000.00],
                                        ],
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        config(['services.gemini.api_key' => 'fake-test-key']);

        $file = UploadedFile::fake()->image('receipt.jpg', 600, 800);

        $response = $this->postJson('/api/receipts/scan', [
            'image' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.merchant_name', 'Indomaret Point')
            ->assertJsonPath('data.total_amount', 54000)
            ->assertJsonCount(2, 'data.items');
    }
}
