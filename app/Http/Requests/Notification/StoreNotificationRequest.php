<?php

declare(strict_types=1);

namespace App\Http\Requests\Notification;

use App\Enums\ChannelName;
use App\Rules\RecipientRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreNotificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'min:1'],
            'channel' => ['required', new Enum(ChannelName::class)],
            'recipient' => ['required', 'string', new RecipientRule($this->input('channel'))],
            'message' => ['required', 'string', 'max:500'],
        ];
    }
}
