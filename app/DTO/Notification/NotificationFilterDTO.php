<?php

declare(strict_types=1);

namespace App\DTO\Notification;

use App\Enums\ChannelName;
use App\Enums\NotificationStatus;
use App\Http\Requests\Notification\IndexNotificationRequest;

final class NotificationFilterDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly ?NotificationStatus $status,
        public readonly ?ChannelName $channel,
        public readonly int $perPage = 15,
    ) {}

    public static function fromRequest(IndexNotificationRequest $request): self
    {
        return new self(
            userId: $request->integer('user_id'),
            status: NotificationStatus::tryFrom($request->input('filter.status', '')),
            channel: ChannelName::tryFrom($request->input('filter.channel', '')),
            perPage: $request->integer('per_page', 15),
        );
    }
}
