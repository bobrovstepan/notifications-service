<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\ChannelName;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class RecipientRule implements ValidationRule
{
    public function __construct(private readonly ?string $channel) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $channel = ChannelName::tryFrom($this->channel ?? '');

        if ($channel === null) {
            return;
        }

        $validator = Validator::make(
            [$attribute => $value],
            [$attribute => $channel->recipientRule()],
        );

        if ($validator->fails()) {
            $fail($validator->errors()->first($attribute));
        }
    }
}
