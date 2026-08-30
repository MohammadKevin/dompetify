<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notification_text' => ['required', 'string', 'max:2000'],
            'sender_app' => ['nullable', 'string', 'max:100'],
            'auto_record' => ['nullable', 'boolean'],
        ];
    }
}
