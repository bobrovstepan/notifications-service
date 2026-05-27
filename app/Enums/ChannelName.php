<?php

namespace App\Enums;

enum ChannelName: string
{
    case Email    = 'email';
    case Telegram = 'telegram';

    public function recipientRule(): string
    {
        return match($this) {
            self::Email    => 'email',
            self::Telegram => 'regex:/^\d+$/',
        };
    }
}
