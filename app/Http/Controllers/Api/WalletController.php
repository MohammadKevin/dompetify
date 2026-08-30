<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of all wallets with net worth calculation.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Wallet::query();

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        $wallets = $query->orderBy('is_active', 'desc')->orderBy('name', 'asc')->get();

        $activeWallets = $wallets->where('is_active', true);
        $totalNetWorth = (float) $activeWallets->sum('balance');

        $byTypeSummary = [];
        foreach ($activeWallets->groupBy('type') as $type => $groupedWallets) {
            $typeName = is_object($type) && isset($type->value) ? $type->value : (string) $type;
            $byTypeSummary[$typeName] = (float) $groupedWallets->sum('balance');
        }

        return response()->json([
            'success' => true,
            'data' => WalletResource::collection($wallets),
            'meta' => [
                'total_net_worth' => $totalNetWorth,
                'total_wallets' => $wallets->count(),
                'active_wallets_count' => $activeWallets->count(),
                'summary_by_type' => $byTypeSummary,
            ],
        ]);
    }

    /**
     * Store a newly created wallet.
     */
    public function store(StoreWalletRequest $request): JsonResponse
    {
        $wallet = Wallet::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Dompet berhasil dibuat.',
            'data' => new WalletResource($wallet),
        ], 201);
    }

    /**
     * Display the specified wallet.
     */
    public function show(Wallet $wallet): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new WalletResource($wallet),
        ]);
    }

    /**
     * Update the specified wallet.
     */
    public function update(UpdateWalletRequest $request, Wallet $wallet): JsonResponse
    {
        $wallet->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Dompet berhasil diperbarui.',
            'data' => new WalletResource($wallet),
        ]);
    }

    /**
     * Remove or archive the specified wallet.
     */
    public function destroy(Request $request, Wallet $wallet): JsonResponse
    {
        $force = filter_var($request->query('force', false), FILTER_VALIDATE_BOOLEAN);

        if ($force) {
            $wallet->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dompet berhasil dihapus permanen.',
            ]);
        }

        // Default: soft deactivation/archive to preserve transaction ledger history
        $wallet->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Dompet berhasil dinonaktifkan.',
            'data' => new WalletResource($wallet),
        ]);
    }
}
