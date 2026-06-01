<?php

declare(strict_types=1);

namespace App\DTO\Notification;

final readonly class SendNotificationDTO
{
    public function __construct(
        public int $notificationId,
        public string $recipient,
        public string $message,
    ) {}
}
