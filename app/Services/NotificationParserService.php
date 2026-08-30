<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;

class NotificationParserService
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    /**
     * Parse notification text and optionally record the transaction.
     *
     * @return array{
     *     parsed: array{
     *         detected_app: string,
     *         type: TransactionType,
     *         amount: float,
     *         description: string,
     *         wallet: Wallet|null,
     *         category: Category|null,
     *         date: string
     *     },
     *     transaction: Transaction|null
     * }
     */
    public function parseAndProcess(string $rawNotification, ?string $senderApp = null, bool $autoRecord = true): array
    {
        $parsed = $this->parseNotification($rawNotification, $senderApp);

        $transaction = null;
        if ($autoRecord && $parsed['wallet'] instanceof Wallet && $parsed['amount'] > 0) {
            $transaction = $this->transactionService->createTransaction([
                'wallet_id' => $parsed['wallet']->id,
                'category_id' => $parsed['category']?->id,
                'type' => $parsed['type'],
                'amount' => $parsed['amount'],
                'admin_fee' => 0.00,
                'date' => $parsed['date'],
                'description' => $parsed['description'],
            ]);
        }

        return [
            'parsed' => $parsed,
            'transaction' => $transaction,
        ];
    }

    /**
     * Parse raw notification string into structured transaction attributes.
     *
     * @return array{
     *     detected_app: string,
     *     type: TransactionType,
     *     amount: float,
     *     description: string,
     *     wallet: Wallet|null,
     *     category: Category|null,
     *     date: string
     * }
     */
    public function parseNotification(string $text, ?string $appHint = null): array
    {
        $detectedApp = $this->detectApp($text, $appHint);
        $type = $this->detectTransactionType($text);
        $amount = $this->extractAmount($text);
        $description = $this->extractDescription($text, $detectedApp);
        $wallet = $this->resolveWallet($detectedApp);
        $category = $this->resolveCategory($text, $type);

        return [
            'detected_app' => $detectedApp,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'wallet' => $wallet,
            'category' => $category,
            'date' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Detect provider / banking / e-wallet app.
     */
    public function detectApp(string $text, ?string $hint = null): string
    {
        $haystack = mb_strtolower(($hint ?? '').' '.$text);

        if (str_contains($haystack, 'brimo') || str_contains($haystack, 'bri ') || str_contains($haystack, 'bank bri')) {
            return 'BRImo';
        }
        if (str_contains($haystack, 'bca') || str_contains($haystack, 'klikbca') || str_contains($haystack, 'mybca')) {
            return 'BCA';
        }
        if (str_contains($haystack, 'gopay') || str_contains($haystack, 'gojek')) {
            return 'GoPay';
        }
        if (str_contains($haystack, 'ovo')) {
            return 'OVO';
        }
        if (str_contains($haystack, 'dana')) {
            return 'DANA';
        }
        if (str_contains($haystack, 'shopeepay') || str_contains($haystack, 'shopee')) {
            return 'ShopeePay';
        }
        if (str_contains($haystack, 'livin') || str_contains($haystack, 'mandiri')) {
            return 'Mandiri';
        }
        if (str_contains($haystack, 'seabank')) {
            return 'SeaBank';
        }
        if (str_contains($haystack, 'blu')) {
            return 'Blu';
        }

        return $hint ?: 'Other';
    }

    /**
     * Detect whether the notification represents an EXPENSE (Debit) or INCOME (Credit).
     */
    public function detectTransactionType(string $text): TransactionType
    {
        $normalized = mb_strtolower($text);

        // Income patterns: incoming transfer, received funds, top up, credit mutation
        $incomeKeywords = [
            'masuk',
            'menerima',
            'diterima',
            'terima uang',
            'credit',
            'kredit',
            'top up',
            'topup',
            'isi saldo',
            'transfer dari',
            'setoran',
            'cashback',
            'dana masuk',
            'uang masuk',
        ];

        foreach ($incomeKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return TransactionType::INCOME;
            }
        }

        // Expense patterns: debit mutation, payment, transfer to, purchase
        $expenseKeywords = [
            'debet',
            'debit',
            'bayar',
            'membayar',
            'pembayaran',
            'transfer ke',
            'kirim uang',
            'keluar',
            'berhasil bayar',
            'transaksi berhasil',
            'tagihan',
            'pembelian',
            'tarik tunai',
            'qris',
        ];

        foreach ($expenseKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return TransactionType::EXPENSE;
            }
        }

        return TransactionType::EXPENSE;
    }

    /**
     * Extract nominal amount from Indonesian format strings.
     * Examples: 'Rp 50.000', 'Rp. 150.000,00', 'Rp1.250.000', 'IDR 75,000.00', 'sebesar Rp 20.000'
     */
    public function extractAmount(string $text): float
    {
        // Pattern 1: Matches Rp / IDR followed by numbers with Indonesian/International separators
        if (preg_match('/(?:Rp\.?|IDR)\s*([0-9]{1,3}(?:[.,][0-9]{3})*(?:[.,][0-9]{2})?|[0-9]+)/i', $text, $matches)) {
            return $this->normalizeIndonesianNumber($matches[1]);
        }

        // Pattern 2: Matches "sebesar 50.000" or "nominal 50.000"
        if (preg_match('/(?:sebesar|nominal|jumlah|total)\s*:?\s*([0-9]{1,3}(?:[.,][0-9]{3})*(?:[.,][0-9]{2})?|[0-9]+)/i', $text, $matches)) {
            return $this->normalizeIndonesianNumber($matches[1]);
        }

        // Pattern 3: Standalone 4+ digit number with dot separator e.g. "50.000"
        if (preg_match('/\b([0-9]{1,3}\.[0-9]{3}(?:\.[0-9]{3})*(?:,[0-9]{2})?)\b/', $text, $matches)) {
            return $this->normalizeIndonesianNumber($matches[1]);
        }

        return 0.00;
    }

    /**
     * Convert Indonesian formatted number strings (e.g. '1.500.000,00' or '1,500,000.00' or '50.000') to float.
     */
    protected function normalizeIndonesianNumber(string $numStr): float
    {
        $numStr = trim($numStr);

        // Case A: Contains both '.' and ',' (e.g. '1.500.000,00' or '1,500,000.00')
        if (str_contains($numStr, '.') && str_contains($numStr, ',')) {
            $lastDot = strrpos($numStr, '.');
            $lastComma = strrpos($numStr, ',');

            if ($lastComma > $lastDot) {
                // Indonesian format: 1.500.000,00
                $numStr = str_replace('.', '', $numStr);
                $numStr = str_replace(',', '.', $numStr);
            } else {
                // US format: 1,500,000.00
                $numStr = str_replace(',', '', $numStr);
            }
        } elseif (str_contains($numStr, '.')) {
            // Case B: Only dots. In Indonesia, dot is thousand separator e.g. 50.000 or 1.500.000
            // If the last dot has exactly 3 digits after it, it's thousands separator
            $parts = explode('.', $numStr);
            $lastPart = end($parts);
            if (strlen($lastPart) === 3 || count($parts) > 2) {
                $numStr = str_replace('.', '', $numStr);
            }
        } elseif (str_contains($numStr, ',')) {
            // Case C: Only commas. E.g. 50,000 (thousands) or 50,50 (decimals)
            $parts = explode(',', $numStr);
            $lastPart = end($parts);
            if (strlen($lastPart) === 3) {
                $numStr = str_replace(',', '', $numStr);
            } else {
                $numStr = str_replace(',', '.', $numStr);
            }
        }

        return (float) $numStr;
    }

    /**
     * Extract a meaningful transaction description from the notification.
     */
    public function extractDescription(string $text, string $appName): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $text));

        // Limit description length if very verbose push notification
        if (mb_strlen($clean) > 200) {
            return mb_substr($clean, 0, 197).'...';
        }

        return $clean ?: "Transaksi via {$appName}";
    }

    /**
     * Find or match an active wallet in database based on detected provider app.
     */
    public function resolveWallet(string $appName): ?Wallet
    {
        if ($appName === 'Other') {
            return Wallet::active()->first();
        }

        // Try exact match on wallet name
        $wallet = Wallet::active()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($appName)])
            ->first();

        if ($wallet) {
            return $wallet;
        }

        // Try partial match on wallet name
        $wallet = Wallet::active()
            ->where('name', 'LIKE', "%{$appName}%")
            ->first();

        if ($wallet) {
            return $wallet;
        }

        // Fallback to first active wallet
        return Wallet::active()->first();
    }

    /**
     * Resolve a relevant category based on keywords in notification text.
     */
    public function resolveCategory(string $text, TransactionType $type): ?Category
    {
        $normalized = mb_strtolower($text);

        if ($type === TransactionType::INCOME) {
            if (str_contains($normalized, 'gaji') || str_contains($normalized, 'payroll') || str_contains($normalized, 'salary')) {
                return Category::income()->where('name', 'LIKE', '%Gaji%')->first();
            }
            if (str_contains($normalized, 'bonus') || str_contains($normalized, 'thr')) {
                return Category::income()->where('name', 'LIKE', '%Bonus%')->first();
            }
            if (str_contains($normalized, 'investasi') || str_contains($normalized, 'dividen') || str_contains($normalized, 'reksadana')) {
                return Category::income()->where('name', 'LIKE', '%Investasi%')->first();
            }

            return Category::income()->first();
        }

        // Expense category heuristics
        if (str_contains($normalized, 'food') || str_contains($normalized, 'kopi') || str_contains($normalized, 'resto') || str_contains($normalized, 'cafe') || str_contains($normalized, 'makan') || str_contains($normalized, 'grabfood') || str_contains($normalized, 'gofood') || str_contains($normalized, 'shopeefood')) {
            return Category::expense()->where('name', 'LIKE', '%Makanan%')->first() ?? Category::expense()->first();
        }

        if (str_contains($normalized, 'alfamart') || str_contains($normalized, 'indomaret') || str_contains($normalized, 'supermarket') || str_contains($normalized, 'belanja') || str_contains($normalized, 'tokopedia') || str_contains($normalized, 'shopee') || str_contains($normalized, 'mall')) {
            return Category::expense()->where('name', 'LIKE', '%Belanja%')->first() ?? Category::expense()->first();
        }

        if (str_contains($normalized, 'pln') || str_contains($normalized, 'listrik') || str_contains($normalized, 'pdam') || str_contains($normalized, 'air') || str_contains($normalized, 'wifi') || str_contains($normalized, 'indihome') || str_contains($normalized, 'bpjs') || str_contains($normalized, 'tagihan') || str_contains($normalized, 'pulsa')) {
            return Category::expense()->where('name', 'LIKE', '%Tagihan%')->first() ?? Category::expense()->first();
        }

        if (str_contains($normalized, 'bensin') || str_contains($normalized, 'pertamina') || str_contains($normalized, 'spbu') || str_contains($normalized, 'toll') || str_contains($normalized, 'tol') || str_contains($normalized, 'grab') || str_contains($normalized, 'gojek') || str_contains($normalized, 'parkir') || str_contains($normalized, 'transport')) {
            return Category::expense()->where('name', 'LIKE', '%Transportasi%')->first() ?? Category::expense()->first();
        }

        if (str_contains($normalized, 'bioskop') || str_contains($normalized, 'cinema') || str_contains($normalized, 'xxi') || str_contains($normalized, 'cgv') || str_contains($normalized, 'netflix') || str_contains($normalized, 'spotify') || str_contains($normalized, 'game') || str_contains($normalized, 'steam')) {
            return Category::expense()->where('name', 'LIKE', '%Hiburan%')->first() ?? Category::expense()->first();
        }

        return Category::expense()->first();
    }
}
