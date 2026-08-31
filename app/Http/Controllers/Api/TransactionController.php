<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\TransactionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    /**
     * Display a listing of transactions with filtering, search, and summary stats scoped to current user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Transaction::with(['wallet', 'targetWallet', 'category', 'items']);

        if ($user) {
            $query->where('user_id', $user->id);
        }

        // Filter by specific wallet (either source or destination in transfer)
        if ($request->filled('wallet_id')) {
            $walletId = $request->query('wallet_id');
            $query->where(function ($q) use ($walletId) {
                $q->where('wallet_id', $walletId)
                    ->orWhere('target_wallet_id', $walletId);
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        // Filter by transaction type (EXPENSE, INCOME, TRANSFER)
        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->query('start_date').' 00:00:00');
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->query('end_date').' 23:59:59');
        }

        // Search in description or item names
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where('item_name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Clone query for aggregate period calculations
        $statsQuery = clone $query;
        $allMatching = $statsQuery->get();

        $totalExpense = (float) $allMatching->where('type', TransactionType::EXPENSE)->sum('amount');
        $totalIncome = (float) $allMatching->where('type', TransactionType::INCOME)->sum('amount');
        $totalTransfer = (float) $allMatching->where('type', TransactionType::TRANSFER)->sum('amount');

        $perPage = min((int) $request->query('per_page', 15), 100);
        $transactions = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => TransactionResource::collection($transactions),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
            'summary' => [
                'total_expense' => $totalExpense,
                'total_income' => $totalIncome,
                'total_transfer' => $totalTransfer,
                'net_flow' => $totalIncome - $totalExpense,
            ],
        ]);
    }

    /**
     * Store a newly created transaction and atomically sync balances.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            if ($user) {
                $data['user_id'] = $user->id;

                // Validate source wallet ownership
                $wallet = Wallet::findOrFail($data['wallet_id']);
                if ($wallet->user_id && $wallet->user_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke dompet sumber ini.',
                    ], 403);
                }

                // Validate target wallet ownership if transfer
                if (! empty($data['target_wallet_id'])) {
                    $targetWallet = Wallet::findOrFail($data['target_wallet_id']);
                    if ($targetWallet->user_id && $targetWallet->user_id !== $user->id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki akses ke dompet tujuan ini.',
                        ], 403);
                    }
                }
            }

            $transaction = $this->transactionService->createTransaction($data);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dicatat dan saldo dompet telah diperbarui.',
                'data' => new TransactionResource($transaction),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat transaksi: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified transaction.
     */
    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorizeTransactionOwnership($request, $transaction);

        $transaction->load(['wallet', 'targetWallet', 'category', 'items']);

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction),
        ]);
    }

    /**
     * Remove the specified transaction and reverse balance mutations.
     */
    public function destroy(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorizeTransactionOwnership($request, $transaction);

        try {
            $this->transactionService->deleteTransaction($transaction);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus dan saldo dompet telah dikembalikan.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper to verify transaction ownership.
     */
    protected function authorizeTransactionOwnership(Request $request, Transaction $transaction): void
    {
        $user = $request->user();
        if ($user && $transaction->user_id && $transaction->user_id !== $user->id) {
            abort(403, 'Akses tidak diizinkan ke transaksi pengguna lain.');
        }
    }
}
