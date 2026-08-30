<?php

namespace Database\Seeders;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WalletSeeder::class,
            CategorySeeder::class,
        ]);

        $bca = Wallet::where('name', 'BCA')->first();
        $gopay = Wallet::where('name', 'GoPay')->first();
        $cash = Wallet::where('name', 'Dompet Tunai')->first();

        $foodCat = Category::where('name', 'Makanan & Minuman')->first();
        $salaryCat = Category::where('name', 'Gaji Pokok')->first();
        $shoppingCat = Category::where('name', 'Belanja & Kebutuhan')->first();

        // Sample initial salary transaction
        if ($bca && $salaryCat) {
            Transaction::firstOrCreate([
                'description' => 'Gaji Bulan Ini',
                'wallet_id' => $bca->id,
            ], [
                'category_id' => $salaryCat->id,
                'type' => TransactionType::INCOME->value,
                'amount' => 12000000.00,
                'admin_fee' => 0.00,
                'date' => now()->subDays(5),
            ]);
        }

        // Sample receipt transaction with itemized list
        if ($gopay && $foodCat) {
            $tx = Transaction::firstOrCreate([
                'description' => 'Makan Siang & Kopi di Kopi Kenangan',
                'wallet_id' => $gopay->id,
            ], [
                'category_id' => $foodCat->id,
                'type' => TransactionType::EXPENSE->value,
                'amount' => 65000.00,
                'admin_fee' => 0.00,
                'date' => now()->subDays(1),
            ]);

            TransactionItem::firstOrCreate([
                'transaction_id' => $tx->id,
                'item_name' => 'Kopi Kenangan Mantan Large',
            ], [
                'quantity' => 1,
                'price' => 24000.00,
            ]);

            TransactionItem::firstOrCreate([
                'transaction_id' => $tx->id,
                'item_name' => 'Roti Coklat Klasik',
            ], [
                'quantity' => 2,
                'price' => 20500.00,
            ]);
        }
    }
}
