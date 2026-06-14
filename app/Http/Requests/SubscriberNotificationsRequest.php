<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\NotificationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SubscriberNotificationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', new Enum(NotificationStatus::class)],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.Illuminate\Validation\Rules\Enum' => 'Status must be one of: queued, sent, delivered, discarded.',
            'per_page.max' => 'Maximum 100 records per page.',
        ];
    }

    public function status(): ?NotificationStatus
    {
        $status = $this->validated('status');

        return $status ? NotificationStatus::from($status) : null;
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', 20);
    }
}
