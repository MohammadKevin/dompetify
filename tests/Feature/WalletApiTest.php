<?php

namespace Tests\Feature;

use App\Enums\WalletType;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WalletApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_wallets_with_net_worth_calculation(): void
    {
        Wallet::create([
            'user_id' => $this->user->id,
            'name' => 'BCA',
            'type' => WalletType::BANK,
            'balance' => 5000000.00,
            'is_active' => true,
        ]);

        Wallet::create([
            'user_id' => $this->user->id,
            'name' => 'GoPay',
            'type' => WalletType::E_WALLET,
            'balance' => 300000.00,
            'is_active' => true,
        ]);

        Wallet::create([
            'user_id' => $this->user->id,
            'name' => 'Archived Wallet',
            'type' => WalletType::OTHER,
            'balance' => 1000000.00,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/wallets');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'meta' => [
                    'total_net_worth' => 5300000.00,
                    'total_wallets' => 3,
                    'active_wallets_count' => 2,
                ],
            ]);
    }

    public function test_can_create_new_wallet(): void
    {
        $payload = [
            'name' => 'BRImo',
            'type' => 'BANK',
            'account_number' => '9876543210',
            'balance' => 2500000.00,
            'color_hex' => '#005596',
            'icon' => 'account_balance',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/wallets', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'BRImo')
            ->assertJsonPath('data.balance', 2500000);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $this->user->id,
            'name' => 'BRImo',
            'account_number' => '9876543210',
        ]);
    }

    public function test_can_update_wallet(): void
    {
        $wallet = Wallet::create([
            'user_id' => $this->user->id,
            'name' => 'Cash Lama',
            'type' => WalletType::CASH,
            'balance' => 100000.00,
        ]);

        $response = $this->putJson("/api/wallets/{$wallet->id}", [
            'name' => 'Dompet Tunai Utama',
            'balance' => 250000.00,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Dompet Tunai Utama')
            ->assertJsonPath('data.balance', 250000);
    }

    public function test_can_archive_and_force_delete_wallet(): void
    {
        $wallet = Wallet::create([
            'user_id' => $this->user->id,
            'name' => 'OVO',
            'type' => WalletType::E_WALLET,
            'balance' => 50000.00,
            'is_active' => true,
        ]);

        // Soft archive
        $response = $this->deleteJson("/api/wallets/{$wallet->id}");
        $response->assertStatus(200)
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'is_active' => false,
        ]);

        // Force delete
        $forceResponse = $this->deleteJson("/api/wallets/{$wallet->id}?force=true");
        $forceResponse->assertStatus(200);

        $this->assertDatabaseMissing('wallets', [
            'id' => $wallet->id,
        ]);
    }
}
