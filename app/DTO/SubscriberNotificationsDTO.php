<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\NotificationStatus;
use App\Http\Requests\SubscriberNotificationsRequest;

final readonly class SubscriberNotificationsDTO
{
    public function __construct(
        public string $subscriberId,
        public ?NotificationStatus $status,
        public int $perPage,
    ) {}

    public static function fromRequest(SubscriberNotificationsRequest $request, string $subscriberId): self
    {
        return new self(
            subscriberId: $subscriberId,
            status: $request->status(),
            perPage: $request->perPage(),
        );
    }
}
