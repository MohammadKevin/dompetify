<?php

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Enums\WalletType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Wallet $walletBca;

    protected Wallet $walletGopay;

    protected Category $foodCategory;

    protected Category $salaryCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        $this->walletBca = Wallet::create([
            'user_id' => $this->user->id,
            'name' => 'BCA',
            'type' => WalletType::BANK,
            'balance' => 1000000.00,
            'is_active' => true,
        ]);

        $this->walletGopay = Wallet::create([
            'user_id' => $this->user->id,
            'name' => 'GoPay',
            'type' => WalletType::E_WALLET,
            'balance' => 200000.00,
            'is_active' => true,
        ]);

        $this->foodCategory = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Makanan & Minuman',
            'type' => CategoryType::EXPENSE,
        ]);

        $this->salaryCategory = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Gaji Pokok',
            'type' => CategoryType::INCOME,
        ]);
    }

    public function test_expense_transaction_deducts_source_wallet_balance(): void
    {
        $payload = [
            'wallet_id' => $this->walletBca->id,
            'category_id' => $this->foodCategory->id,
            'type' => 'EXPENSE',
            'amount' => 150000.00,
            'description' => 'Makan malam di Resto Padang',
            'items' => [
                ['item_name' => 'Nasi Rendang', 'quantity' => 2, 'price' => 50000.00],
                ['item_name' => 'Es Teh Manis', 'quantity' => 2, 'price' => 25000.00],
            ],
        ];

        $response = $this->postJson('/api/transactions', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.amount', 150000)
            ->assertJsonCount(2, 'data.items');

        $this->walletBca->refresh();
        $this->assertEquals(850000.00, (float) $this->walletBca->balance);

        $this->assertDatabaseHas('transaction_items', [
            'item_name' => 'Nasi Rendang',
            'quantity' => 2,
        ]);
    }

    public function test_income_transaction_adds_to_source_wallet_balance(): void
    {
        $payload = [
            'wallet_id' => $this->walletBca->id,
            'category_id' => $this->salaryCategory->id,
            'type' => 'INCOME',
            'amount' => 5000000.00,
            'description' => 'Gaji Freelance',
        ];

        $response = $this->postJson('/api/transactions', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.amount', 5000000);

        $this->walletBca->refresh();
        $this->assertEquals(6000000.00, (float) $this->walletBca->balance);
    }

    public function test_transfer_transaction_updates_both_wallets_with_admin_fee(): void
    {
        $payload = [
            'wallet_id' => $this->walletBca->id,
            'target_wallet_id' => $this->walletGopay->id,
            'type' => 'TRANSFER',
            'amount' => 100000.00,
            'admin_fee' => 2500.00,
            'description' => 'Top Up GoPay via BCA Mobile',
        ];

        $response = $this->postJson('/api/transactions', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.amount', 100000)
            ->assertJsonPath('data.admin_fee', 2500);

        $this->walletBca->refresh();
        $this->walletGopay->refresh();

        // Source BCA: 1.000.000 - (100.000 + 2.500) = 897.500
        $this->assertEquals(897500.00, (float) $this->walletBca->balance);
        // Target GoPay: 200.000 + 100.000 = 300.000
        $this->assertEquals(300000.00, (float) $this->walletGopay->balance);
    }

    public function test_deleting_transaction_reverses_balance_mutations(): void
    {
        // 1. Create expense
        $response = $this->postJson('/api/transactions', [
            'wallet_id' => $this->walletBca->id,
            'category_id' => $this->foodCategory->id,
            'type' => 'EXPENSE',
            'amount' => 200000.00,
            'description' => 'Belanja Supermarket',
        ]);

        $txId = $response->json('data.id');
        $this->walletBca->refresh();
        $this->assertEquals(800000.00, (float) $this->walletBca->balance);

        // 2. Delete transaction
        $delResponse = $this->deleteJson("/api/transactions/{$txId}");
        $delResponse->assertStatus(200);

        // 3. Verify balance is restored
        $this->walletBca->refresh();
        $this->assertEquals(1000000.00, (float) $this->walletBca->balance);
        $this->assertDatabaseMissing('transactions', ['id' => $txId]);
    }

    public function test_listing_transactions_with_filters_and_summary(): void
    {
        // Seed some transactions
        $this->postJson('/api/transactions', [
            'wallet_id' => $this->walletBca->id,
            'category_id' => $this->foodCategory->id,
            'type' => 'EXPENSE',
            'amount' => 50000.00,
            'date' => '2026-08-10 12:00:00',
        ]);

        $this->postJson('/api/transactions', [
            'wallet_id' => $this->walletBca->id,
            'category_id' => $this->salaryCategory->id,
            'type' => 'INCOME',
            'amount' => 1000000.00,
            'date' => '2026-08-15 12:00:00',
        ]);

        $response = $this->getJson('/api/transactions?type=EXPENSE');

        $response->assertStatus(200)
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('summary.total_expense', 50000);
    }
}
