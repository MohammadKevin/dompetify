<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MultiTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_or_mutate_other_users_wallets(): void
    {
        $userA = User::factory()->create(['name' => 'User A']);
        $userB = User::factory()->create(['name' => 'User B']);

        $walletA = Wallet::create([
            'user_id' => $userA->id,
            'name' => 'BCA User A',
            'type' => WalletType::BANK,
            'balance' => 5000000.00,
        ]);

        $walletB = Wallet::create([
            'user_id' => $userB->id,
            'name' => 'BCA User B',
            'type' => WalletType::BANK,
            'balance' => 10000000.00,
        ]);

        // Authenticate as User A
        Sanctum::actingAs($userA);

        // 1. Listing wallets: User A should only see wallet A
        $listResponse = $this->getJson('/api/wallets');
        $listResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'BCA User A');

        // 2. User A cannot view User B's wallet directly
        $showResponse = $this->getJson("/api/wallets/{$walletB->id}");
        $showResponse->assertStatus(403);

        // 3. User A cannot update User B's wallet
        $updateResponse = $this->putJson("/api/wallets/{$walletB->id}", [
            'name' => 'Hacked Wallet',
        ]);
        $updateResponse->assertStatus(403);

        // 4. User A cannot delete User B's wallet
        $deleteResponse = $this->deleteJson("/api/wallets/{$walletB->id}");
        $deleteResponse->assertStatus(403);
    }

    public function test_user_cannot_record_transactions_on_other_users_wallets(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $walletB = Wallet::create([
            'user_id' => $userB->id,
            'name' => 'BCA User B',
            'type' => WalletType::BANK,
            'balance' => 10000000.00,
        ]);

        Sanctum::actingAs($userA);

        $txResponse = $this->postJson('/api/transactions', [
            'wallet_id' => $walletB->id,
            'type' => TransactionType::EXPENSE->value,
            'amount' => 500000.00,
            'description' => 'Unauthorized Expense',
        ]);

        $txResponse->assertStatus(403);

        // Verify balance remained unchanged
        $this->assertEquals(10000000.00, (float) $walletB->fresh()->balance);
    }
}
