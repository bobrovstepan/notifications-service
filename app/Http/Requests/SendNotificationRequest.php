<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\NotificationChannel;
use App\Enums\NotificationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'channel' => ['required', 'string', new Enum(NotificationChannel::class)],
            'type' => ['required', 'string', new Enum(NotificationType::class)],
            'message' => ['required', 'string', 'min:1', 'max:1000'],
            'subscriber_ids' => ['required', 'array', 'min:1', 'max:1000'],
            'subscriber_ids.*' => ['required', 'string', 'max:64'],
        ];
    }

    public function messages(): array
    {
        return [
            'channel.Illuminate\Validation\Rules\Enum' => 'Channel must be one of: sms, email.',
            'type.Illuminate\Validation\Rules\Enum' => 'Type must be one of: transactional, marketing.',
            'message.max' => 'Message must not exceed 1000 characters.',
            'subscriber_ids.min' => 'At least one subscriber is required.',
            'subscriber_ids.max' => 'Maximum 1000 subscribers per request.',
            'subscriber_ids.*.max' => 'Subscriber ID must not exceed 64 characters.',
        ];
    }

    public function channel(): NotificationChannel
    {
        return NotificationChannel::from($this->validated('channel'));
    }

    public function type(): NotificationType
    {
        return NotificationType::from($this->validated('type'));
    }

    public function message(): string
    {
        return $this->validated('message');
    }

    public function subscriberIds(): array
    {
        return $this->validated('subscriber_ids');
    }

    public function idempotencyKey(): ?string
    {
        return $this->header('X-Idempotency-Key');
    }
}
