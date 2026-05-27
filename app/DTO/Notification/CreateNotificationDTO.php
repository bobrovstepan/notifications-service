<?php

declare(strict_types=1);

namespace App\DTO\Notification;

use App\Http\Requests\Notification\StoreNotificationRequest;

final class CreateNotificationDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $channel,
        public readonly string $recipient,
        public readonly string $message,
    ) {}

    public static function fromRequest(StoreNotificationRequest $request): self
    {
        return new self(
            userId: $request->integer('user_id'),
            channel: $request->string('channel')->value(),
            recipient: $request->string('recipient')->value(),
            message: $request->string('message')->value(),
        );
    }
}
