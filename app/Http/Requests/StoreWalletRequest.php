<?php

namespace App\Http\Requests;

use App\Enums\WalletType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::enum(WalletType::class)],
            'account_number' => ['nullable', 'string', 'max:50'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'color_hex' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
