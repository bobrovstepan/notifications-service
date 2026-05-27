<?php

namespace App\Http\Requests\Notification;

use App\Enums\ChannelName;
use App\Enums\NotificationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class IndexNotificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id'         => ['required', 'integer', 'min:1'],
            'filter.status'   => ['nullable', new Enum(NotificationStatus::class)],
            'filter.channel'  => ['nullable', new Enum(ChannelName::class)],
            'per_page'        => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
