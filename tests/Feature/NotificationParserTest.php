<?php

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\Category;
use App\Models\Wallet;
use App\Services\NotificationParserService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationParserTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationParserService $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new NotificationParserService(new TransactionService);

        Wallet::create([
            'name' => 'BCA',
            'type' => WalletType::BANK,
            'balance' => 1000000.00,
            'is_active' => true,
        ]);

        Wallet::create([
            'name' => 'BRImo',
            'type' => WalletType::BANK,
            'balance' => 500000.00,
            'is_active' => true,
        ]);

        Wallet::create([
            'name' => 'GoPay',
            'type' => WalletType::E_WALLET,
            'balance' => 100000.00,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Makanan & Minuman',
            'type' => CategoryType::EXPENSE,
        ]);

        Category::create([
            'name' => 'Gaji Pokok',
            'type' => CategoryType::INCOME,
        ]);
    }

    public function test_parses_brimo_debit_notification(): void
    {
        $text = 'BRImo: Transaksi debit Rp 75.000 di Alfamart berhasil. Sisa saldo Anda Rp 425.000.';
        $result = $this->parser->parseNotification($text, 'BRImo');

        $this->assertEquals('BRImo', $result['detected_app']);
        $this->assertEquals(TransactionType::EXPENSE, $result['type']);
        $this->assertEquals(75000.00, $result['amount']);
        $this->assertEquals('BRImo', $result['wallet']?->name);
    }

    public function test_parses_bca_credit_notification(): void
    {
        $text = 'm-Transfer: Rp 2.500.000,00 masuk ke rek 1234567890 dari PT KARYA MAKMUR - Gaji.';
        $result = $this->parser->parseNotification($text, 'BCA');

        $this->assertEquals('BCA', $result['detected_app']);
        $this->assertEquals(TransactionType::INCOME, $result['type']);
        $this->assertEquals(2500000.00, $result['amount']);
        $this->assertEquals('BCA', $result['wallet']?->name);
    }

    public function test_parses_gopay_payment_notification(): void
    {
        $text = 'Kamu telah membayar Rp 32.500 di Kopi Kenangan pakai GoPay.';
        $result = $this->parser->parseNotification($text);

        $this->assertEquals('GoPay', $result['detected_app']);
        $this->assertEquals(TransactionType::EXPENSE, $result['type']);
        $this->assertEquals(32500.00, $result['amount']);
        $this->assertEquals('GoPay', $result['wallet']?->name);
    }

    public function test_webhook_endpoint_auto_records_transaction(): void
    {
        $payload = [
            'notification_text' => 'Kamu telah membayar Rp 45.000 di Alfamart pakai GoPay.',
            'sender_app' => 'GoPay',
            'auto_record' => true,
        ];

        $response = $this->postJson('/api/webhook/notification', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.amount', 45000)
            ->assertJsonPath('data.detected_app', 'GoPay');

        $gopay = Wallet::where('name', 'GoPay')->first();
        // Initial 100.000 - 45.000 = 55.000
        $this->assertEquals(55000.00, (float) $gopay->balance);
    }
}
