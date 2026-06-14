<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\NotificationStatus;

final readonly class ProviderResult
{
    public function __construct(
        public NotificationStatus $status,
        public ?string $failureReason = null,
    ) {}

    public static function delivered(): self
    {
        return new self(status: NotificationStatus::Delivered);
    }

    public static function discarded(string $reason): self
    {
        return new self(
            status: NotificationStatus::Discarded,
            failureReason: $reason,
        );
    }
}
