<?php

namespace App\DTO\Notification;

use App\Enums\NotificationStatus;
use App\Models\Notification;

final class NotificationData
{
    public function __construct(
        public readonly int    $userId,
        public readonly int    $channelId,
        public readonly string $recipient,
        public readonly string $message,
    ) {}

    public function toArray(): array
    {
        return [
            Notification::FIELD_USER_ID    => $this->userId,
            Notification::FIELD_CHANNEL_ID => $this->channelId,
            Notification::FIELD_RECIPIENT  => $this->recipient,
            Notification::FIELD_MESSAGE    => $this->message,
            Notification::FIELD_STATUS     => NotificationStatus::Processing,
        ];
    }
}
