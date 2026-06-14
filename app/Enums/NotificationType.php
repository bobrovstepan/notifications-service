<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationType: string
{
    case Transactional = 'transactional';
    case Marketing = 'marketing';

    public function label(): string
    {
        return match ($this) {
            self::Transactional => 'Transactional',
            self::Marketing => 'Marketing',
        };
    }

    public function queueName(): string
    {
        return match ($this) {
            self::Transactional => 'notifications.transactional',
            self::Marketing => 'notifications.marketing',
        };
    }

    public function retryDelay(): int
    {
        return match ($this) {
            self::Transactional => 5,
            self::Marketing => 30,
        };
    }

    public function maxAttempts(): int
    {
        return match ($this) {
            self::Transactional => 5,
            self::Marketing => 3,
        };
    }
}
