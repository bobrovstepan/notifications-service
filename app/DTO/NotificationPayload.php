<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\NotificationChannel;

final readonly class NotificationPayload
{
    public function __construct(
        public string $recipientId,
        public string $message,
        public NotificationChannel $channel,
        public string $notificationId,
    ) {}
}
