<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ReceiptScanService
{
    /**
     * Parse receipt image using Gemini Vision Flash API.
     *
     * @return array{
     *     merchant_name: string,
     *     transaction_date: string,
     *     total_amount: float,
     *     suggested_category: string,
     *     items: array<int, array{item_name: string, quantity: int, price: float}>,
     *     receipt_image_path: string|null
     * }
     *
     * @throws RuntimeException
     */
    public function scanReceipt(UploadedFile|string $imageFile): array
    {
        $receiptPath = null;
        $mimeType = 'image/jpeg';
        $base64Image = '';

        if ($imageFile instanceof UploadedFile) {
            $mimeType = $imageFile->getMimeType() ?: 'image/jpeg';
            $base64Image = base64_encode($imageFile->getContent());
            // Store receipt in public disk for retrieval
            $receiptPath = $imageFile->store('receipts', 'public');
        } elseif (is_string($imageFile)) {
            if (file_exists($imageFile)) {
                $mimeType = mime_content_type($imageFile) ?: 'image/jpeg';
                $base64Image = base64_encode(file_get_contents($imageFile));
                $receiptPath = $imageFile;
            } else {
                // Raw base64 string provided
                $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $imageFile);
            }
        }

        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $baseUrl = config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');

        if (empty($apiKey)) {
            Log::warning('GEMINI_API_KEY is not set. Returning fallback mock receipt data for testing.');

            return [
                'merchant_name' => 'Demo Supermarket',
                'transaction_date' => now()->format('Y-m-d H:i:s'),
                'total_amount' => 125000.00,
                'suggested_category' => 'Belanja',
                'items' => [
                    ['item_name' => 'Beras Premium 5kg', 'quantity' => 1, 'price' => 75000.00],
                    ['item_name' => 'Minyak Goreng 2L', 'quantity' => 1, 'price' => 35000.00],
                    ['item_name' => 'Gula Pasir 1kg', 'quantity' => 1, 'price' => 15000.00],
                ],
                'receipt_image_path' => $receiptPath,
            ];
        }

        $prompt = <<<'PROMPT'
You are an expert OCR and financial receipt parser. Analyze this receipt image and extract structured data in JSON format only with NO markdown code fences or backticks.

Strict JSON structure required:
{
  "merchant_name": "string (name of the store, supermarket, restaurant, or business)",
  "transaction_date": "Y-m-d H:i:s (format as YYYY-MM-DD HH:MM:SS, default to current timestamp if missing)",
  "total_amount": 0.00 (numeric float of the grand total paid),
  "suggested_category": "string (Indonesian finance category such as Makanan & Minuman, Belanja, Transportasi, Tagihan, Hiburan, Kesehatan)",
  "items": [
    {
      "item_name": "string (line item or item description)",
      "quantity": 1 (integer quantity),
      "price": 0.00 (numeric line price)
    }
  ]
}
PROMPT;

        $url = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::timeout(35)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $base64Image,
                                ],
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if (! $response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new RuntimeException("Gemini API request failed with status: {$response->status()} - {$response->body()}");
            }

            $responseData = $response->json();
            $rawText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

            $cleanJson = $this->cleanMarkdownJson($rawText);
            $parsed = json_decode($cleanJson, true);

            if (! is_array($parsed)) {
                Log::error('Failed to parse Gemini OCR output into JSON', ['raw_text' => $rawText]);
                throw new RuntimeException('Gemini Vision OCR returned invalid JSON format.');
            }

            return [
                'merchant_name' => (string) ($parsed['merchant_name'] ?? 'Toko / Merchant'),
                'transaction_date' => (string) ($parsed['transaction_date'] ?? now()->format('Y-m-d H:i:s')),
                'total_amount' => (float) ($parsed['total_amount'] ?? 0.00),
                'suggested_category' => (string) ($parsed['suggested_category'] ?? 'Belanja'),
                'items' => isset($parsed['items']) && is_array($parsed['items']) ? $parsed['items'] : [],
                'receipt_image_path' => $receiptPath,
            ];
        } catch (Exception $e) {
            Log::error('Receipt scan exception: '.$e->getMessage());
            throw new RuntimeException('Failed to process receipt with Gemini OCR: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Strip markdown code blocks and surrounding whitespaces.
     */
    protected function cleanMarkdownJson(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);

        return trim($text);
    }
}
