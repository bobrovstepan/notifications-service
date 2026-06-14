<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\SubscriberRepositoryInterface;
use App\Enums\NotificationStatus;
use App\Models\NotificationRecipient;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriberRepository implements SubscriberRepositoryInterface
{
    public function getPaginatedNotifications(
        string $subscriberId,
        ?NotificationStatus $status,
        int $perPage,
    ): LengthAwarePaginator {
        return NotificationRecipient::query()
            ->forSubscriber($subscriberId)
            ->with('notification.channel')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function findForSubscriber(
        string $subscriberId,
        string $notificationId,
    ): NotificationRecipient {
        return NotificationRecipient::query()
            ->forSubscriber($subscriberId)
            ->where('notification_id', $notificationId)
            ->with('notification.channel')
            ->firstOrFail();
    }
}
