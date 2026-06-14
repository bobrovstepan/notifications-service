<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationChannel: string
{
    case Sms = 'sms';
    case Email = 'email';

    public function label(): string
    {
        return match ($this) {
            self::Sms => 'SMS',
            self::Email => 'Email',
        };
    }

    public function queueName(): string
    {
        return 'notifications.'.$this->value;
    }
}
