<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScanReceiptRequest;
use App\Services\ReceiptScanService;
use Exception;
use Illuminate\Http\JsonResponse;

class ReceiptScanController extends Controller
{
    public function __construct(
        protected ReceiptScanService $receiptScanService
    ) {}

    /**
     * Upload a receipt image and extract structured transaction details via Gemini AI Vision OCR.
     */
    public function scan(ScanReceiptRequest $request): JsonResponse
    {
        try {
            $image = $request->file('image');
            $extractedData = $this->receiptScanService->scanReceipt($image);

            return response()->json([
                'success' => true,
                'message' => 'Struk berhasil dipindai dan diekstraksi.',
                'data' => $extractedData,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memindai struk: '.$e->getMessage(),
            ], 500);
        }
    }
}
