<?php

namespace App\Models;

use App\Enums\WalletType;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all wallets owned by the user.
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Get all transactions recorded by the user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get custom categories created by the user.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Auto-provision starter wallets and default settings for a newly registered user.
     */
    public function provisionDefaultData(): void
    {
        $defaultWallets = [
            [
                'name' => 'BCA',
                'type' => WalletType::BANK,
                'account_number' => '1234567890',
                'balance' => 5000000.00,
                'color_hex' => '#00529C',
                'icon' => 'account_balance',
                'is_active' => true,
            ],
            [
                'name' => 'BRImo',
                'type' => WalletType::BANK,
                'account_number' => '0987654321',
                'balance' => 2500000.00,
                'color_hex' => '#005596',
                'icon' => 'account_balance',
                'is_active' => true,
            ],
            [
                'name' => 'GoPay',
                'type' => WalletType::E_WALLET,
                'account_number' => '081234567890',
                'balance' => 350000.00,
                'color_hex' => '#00AED6',
                'icon' => 'account_balance_wallet',
                'is_active' => true,
            ],
            [
                'name' => 'DANA',
                'type' => WalletType::E_WALLET,
                'account_number' => '081234567890',
                'balance' => 150000.00,
                'color_hex' => '#118EEA',
                'icon' => 'payment',
                'is_active' => true,
            ],
            [
                'name' => 'Dompet Tunai',
                'type' => WalletType::CASH,
                'account_number' => null,
                'balance' => 500000.00,
                'color_hex' => '#2E7D32',
                'icon' => 'payments',
                'is_active' => true,
            ],
        ];

        foreach ($defaultWallets as $walletData) {
            $this->wallets()->firstOrCreate(
                ['name' => $walletData['name']],
                $walletData
            );
        }
    }
}
