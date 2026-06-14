<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Enums\NotificationStatus;
use App\Models\NotificationRecipient;
use Illuminate\Pagination\LengthAwarePaginator;

interface SubscriberRepositoryInterface
{
    public function getPaginatedNotifications(
        string $subscriberId,
        ?NotificationStatus $status,
        int $perPage,
    ): LengthAwarePaginator;

    public function findForSubscriber(
        string $subscriberId,
        string $notificationId,
    ): NotificationRecipient;
}
