<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Transaction
 */
class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $receiptUrl = null;
        if ($this->receipt_image_path) {
            $receiptUrl = filter_var($this->receipt_image_path, FILTER_VALIDATE_URL)
                ? $this->receipt_image_path
                : Storage::disk('public')->url($this->receipt_image_path);
        }

        return [
            'id' => $this->id,
            'wallet_id' => $this->wallet_id,
            'category_id' => $this->category_id,
            'target_wallet_id' => $this->target_wallet_id,
            'type' => $this->type->value ?? $this->type,
            'amount' => (float) $this->amount,
            'admin_fee' => (float) $this->admin_fee,
            'date' => $this->date?->toIso8601String(),
            'description' => $this->description,
            'receipt_image_path' => $this->receipt_image_path,
            'receipt_image_url' => $receiptUrl,
            'wallet' => new WalletResource($this->whenLoaded('wallet')),
            'target_wallet' => new WalletResource($this->whenLoaded('targetWallet')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'items' => TransactionItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
