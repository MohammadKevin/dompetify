<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'mimes:jpeg,png,jpg,webp,heic', 'max:10240'],
        ];
    }
}
