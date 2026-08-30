<?php

namespace Database\Seeders;

use App\Enums\WalletType;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wallets = [
            [
                'name' => 'BCA',
                'type' => WalletType::BANK->value,
                'account_number' => '1234567890',
                'balance' => 5000000.00,
                'color_hex' => '#00529C',
                'icon' => 'account_balance',
                'is_active' => true,
            ],
            [
                'name' => 'BRImo',
                'type' => WalletType::BANK->value,
                'account_number' => '0987654321',
                'balance' => 2500000.00,
                'color_hex' => '#005596',
                'icon' => 'account_balance',
                'is_active' => true,
            ],
            [
                'name' => 'GoPay',
                'type' => WalletType::E_WALLET->value,
                'account_number' => '081234567890',
                'balance' => 350000.00,
                'color_hex' => '#00AED6',
                'icon' => 'account_balance_wallet',
                'is_active' => true,
            ],
            [
                'name' => 'OVO',
                'type' => WalletType::E_WALLET->value,
                'account_number' => '081234567890',
                'balance' => 200000.00,
                'color_hex' => '#4C3494',
                'icon' => 'wallet',
                'is_active' => true,
            ],
            [
                'name' => 'DANA',
                'type' => WalletType::E_WALLET->value,
                'account_number' => '081234567890',
                'balance' => 150000.00,
                'color_hex' => '#118EEA',
                'icon' => 'payment',
                'is_active' => true,
            ],
            [
                'name' => 'Dompet Tunai',
                'type' => WalletType::CASH->value,
                'account_number' => null,
                'balance' => 750000.00,
                'color_hex' => '#2E7D32',
                'icon' => 'payments',
                'is_active' => true,
            ],
        ];

        foreach ($wallets as $wallet) {
            Wallet::firstOrCreate(
                ['name' => $wallet['name']],
                $wallet
            );
        }
    }
}
