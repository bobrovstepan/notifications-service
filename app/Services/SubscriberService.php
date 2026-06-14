<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\SubscriberNotificationsDTO;
use App\Models\NotificationRecipient;
use App\Repositories\SubscriberRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriberService
{
    public function __construct(
        private readonly SubscriberRepository $subscriberRepository,
    ) {}

    public function getNotifications(SubscriberNotificationsDTO $dto): LengthAwarePaginator
    {
        return $this->subscriberRepository->getPaginatedNotifications(
            subscriberId: $dto->subscriberId,
            status: $dto->status,
            perPage: $dto->perPage,
        );
    }

    public function getNotification(string $subscriberId, string $notificationId): NotificationRecipient
    {
        return $this->subscriberRepository->findForSubscriber($subscriberId, $notificationId);
    }
}
