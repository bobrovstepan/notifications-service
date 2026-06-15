<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationStatus: string
{
    case Queued = 'queued';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Discarded = 'discarded';

    public function label(): string
    {
        return match ($this) {
            self::Queued => 'Queued',
            self::Sent => 'Sent',
            self::Delivered => 'Delivered',
            self::Discarded => 'Discarded',
        };
    }

    public function isFinal(): bool
    {
        return match ($this) {
            self::Delivered,
            self::Discarded => true,
            default => false,
        };
    }

    public function isTransitionAllowed(self $next): bool
    {
        return match ($this) {
            self::Queued => in_array($next, [self::Sent, self::Discarded]),
            self::Sent => in_array($next, [self::Delivered, self::Discarded]),
            self::Delivered, self::Discarded => false,
        };
    }

    public static function toArray(): array
    {
        return array_map(
            fn (self $status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ],
            self::cases()
        );
    }
}
