<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'wallet_id' => ['required', 'integer', 'exists:wallets,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'target_wallet_id' => [
                'nullable',
                'integer',
                'exists:wallets,id',
                'different:wallet_id',
                'required_if:type,TRANSFER',
            ],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'amount' => ['required', 'numeric', 'gt:0'],
            'admin_fee' => ['nullable', 'numeric', 'min:0'],
            'date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'receipt_image_path' => ['nullable', 'string', 'max:500'],
            'items' => ['nullable', 'array'],
            'items.*.item_name' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.price' => ['required_with:items', 'numeric', 'min:0'],
        ];
    }
}
