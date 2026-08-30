<?php

namespace App\Models;

use App\Enums\WalletType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'account_number',
        'balance',
        'color_hex',
        'icon',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => WalletType::class,
            'balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active wallets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the transactions associated with the wallet as the source wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'wallet_id');
    }

    /**
     * Get incoming transfer transactions where this wallet is the target.
     */
    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(Transaction::class, 'target_wallet_id');
    }
}
