<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationWebhookRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\WalletResource;
use App\Services\NotificationParserService;
use Exception;
use Illuminate\Http\JsonResponse;

class WebhookController extends Controller
{
    public function __construct(
        protected NotificationParserService $notificationParserService
    ) {}

    /**
     * Handle incoming push notification text from Android notification listener webhook.
     */
    public function handleNotification(NotificationWebhookRequest $request): JsonResponse
    {
        try {
            $notificationText = $request->validated('notification_text');
            $senderApp = $request->validated('sender_app');
            $autoRecord = $request->boolean('auto_record', true);

            $result = $this->notificationParserService->parseAndProcess(
                $notificationText,
                $senderApp,
                $autoRecord
            );

            $parsed = $result['parsed'];
            $transaction = $result['transaction'];

            return response()->json([
                'success' => true,
                'message' => $transaction
                    ? 'Notifikasi berhasil diuraikan dan transaksi otomatis tercatat.'
                    : 'Notifikasi berhasil diuraikan.',
                'data' => [
                    'detected_app' => $parsed['detected_app'],
                    'type' => $parsed['type']->value ?? (string) $parsed['type'],
                    'amount' => $parsed['amount'],
                    'description' => $parsed['description'],
                    'date' => $parsed['date'],
                    'wallet' => $parsed['wallet'] ? new WalletResource($parsed['wallet']) : null,
                    'category_id' => $parsed['category']?->id,
                    'suggested_category_name' => $parsed['category']?->name,
                    'transaction' => $transaction ? new TransactionResource($transaction) : null,
                ],
            ], $transaction ? 201 : 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses webhook notifikasi: '.$e->getMessage(),
            ], 422);
        }
    }
}
