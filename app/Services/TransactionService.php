<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransactionService
{
    /**
     * Create a transaction and atomically update the involved wallet balances.
     *
     * @param  array{
     *     wallet_id: int,
     *     category_id?: int|null,
     *     target_wallet_id?: int|null,
     *     type: string|TransactionType,
     *     amount: float|int|string,
     *     admin_fee?: float|int|string,
     *     date?: string|null,
     *     description?: string|null,
     *     receipt_image_path?: string|null,
     *     items?: array<int, array{item_name: string, quantity?: int, price: float|int|string}>
     * }  $data
     *
     * @throws InvalidArgumentException
     */
    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $type = $data['type'] instanceof TransactionType
                ? $data['type']
                : TransactionType::from($data['type']);

            $walletId = (int) $data['wallet_id'];
            $amount = (float) $data['amount'];
            $adminFee = isset($data['admin_fee']) ? (float) $data['admin_fee'] : 0.00;

            $sourceWallet = Wallet::lockForUpdate()->findOrFail($walletId);

            if ($type === TransactionType::EXPENSE) {
                $sourceWallet->decrement('balance', $amount);
            } elseif ($type === TransactionType::INCOME) {
                $sourceWallet->increment('balance', $amount);
            } elseif ($type === TransactionType::TRANSFER) {
                if (empty($data['target_wallet_id'])) {
                    throw new InvalidArgumentException('Target wallet ID is required for transfer transactions.');
                }

                $targetWalletId = (int) $data['target_wallet_id'];

                if ($targetWalletId === $walletId) {
                    throw new InvalidArgumentException('Target wallet cannot be identical to source wallet.');
                }

                $targetWallet = Wallet::lockForUpdate()->findOrFail($targetWalletId);

                // Deduct total transfer cost (amount + admin fee) from source wallet
                $totalTransferDeduction = $amount + $adminFee;
                $sourceWallet->decrement('balance', $totalTransferDeduction);

                // Credit nominal amount to target wallet
                $targetWallet->increment('balance', $amount);
            }

            /** @var Transaction $transaction */
            $transaction = Transaction::create([
                'wallet_id' => $walletId,
                'category_id' => $data['category_id'] ?? null,
                'target_wallet_id' => $data['target_wallet_id'] ?? null,
                'type' => $type->value,
                'amount' => $amount,
                'admin_fee' => $adminFee,
                'date' => $data['date'] ?? now(),
                'description' => $data['description'] ?? null,
                'receipt_image_path' => $data['receipt_image_path'] ?? null,
            ]);

            if (! empty($data['items']) && is_array($data['items'])) {
                $itemsToInsert = [];
                foreach ($data['items'] as $item) {
                    if (empty($item['item_name'])) {
                        continue;
                    }

                    $itemsToInsert[] = [
                        'transaction_id' => $transaction->id,
                        'item_name' => $item['item_name'],
                        'quantity' => (int) ($item['quantity'] ?? 1),
                        'price' => (float) ($item['price'] ?? 0.00),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (! empty($itemsToInsert)) {
                    TransactionItem::insert($itemsToInsert);
                }
            }

            return $transaction->load(['wallet', 'targetWallet', 'category', 'items']);
        });
    }

    /**
     * Delete a transaction and reverse balance mutations atomically.
     */
    public function deleteTransaction(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $sourceWallet = Wallet::lockForUpdate()->find($transaction->wallet_id);

            if ($sourceWallet) {
                if ($transaction->type === TransactionType::EXPENSE) {
                    $sourceWallet->increment('balance', (float) $transaction->amount);
                } elseif ($transaction->type === TransactionType::INCOME) {
                    $sourceWallet->decrement('balance', (float) $transaction->amount);
                } elseif ($transaction->type === TransactionType::TRANSFER) {
                    $totalTransferDeduction = (float) $transaction->amount + (float) $transaction->admin_fee;
                    $sourceWallet->increment('balance', $totalTransferDeduction);

                    if ($transaction->target_wallet_id) {
                        $targetWallet = Wallet::lockForUpdate()->find($transaction->target_wallet_id);
                        if ($targetWallet) {
                            $targetWallet->decrement('balance', (float) $transaction->amount);
                        }
                    }
                }
            }

            return $transaction->delete();
        });
    }
}
