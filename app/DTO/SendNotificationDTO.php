<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\NotificationChannel;
use App\Enums\NotificationType;
use App\Http\Requests\SendNotificationRequest;

final readonly class SendNotificationDTO
{
    public function __construct(
        public NotificationChannel $channel,
        public NotificationType $type,
        public string $message,
        public array $subscriberIds,
        public ?string $idempotencyKey,
    ) {}

    public static function fromRequest(SendNotificationRequest $request): self
    {
        return new self(
            channel: $request->channel(),
            type: $request->type(),
            message: $request->message(),
            subscriberIds: $request->subscriberIds(),
            idempotencyKey: $request->idempotencyKey(),
        );
    }
}
