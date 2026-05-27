<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Notification\CreateNotificationDTO;
use App\DTO\Notification\NotificationData;
use App\DTO\Notification\NotificationFilterDTO;
use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Repositories\Contracts\ChannelRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository,
        private readonly ChannelRepositoryInterface $channelRepository,
    ) {}

    public function create(CreateNotificationDTO $dto): Notification
    {
        $channel = $this->channelRepository->findByNameOrFail($dto->channel);

        $notification = $this->repository->create(new NotificationData(
            userId: $dto->userId,
            channelId: $channel->id,
            recipient: $dto->recipient,
            message: $dto->message,
        ));

        NotificationCreated::dispatch($notification);

        return $notification;
    }

    public function getUserHistory(NotificationFilterDTO $filter): LengthAwarePaginator
    {
        return $this->repository->getUserHistory($filter);
    }

    public function retryStuck(int $minutes): int
    {
        $stuck = $this->repository->findStuck($minutes);

        foreach ($stuck as $notification) {
            NotificationCreated::dispatch($notification);
        }

        return $stuck->count();
    }
}
