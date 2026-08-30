<?php

namespace App\Http\Requests;

use App\Enums\WalletType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'type' => ['sometimes', 'required', Rule::enum(WalletType::class)],
            'account_number' => ['nullable', 'string', 'max:50'],
            'balance' => ['sometimes', 'numeric'],
            'color_hex' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
