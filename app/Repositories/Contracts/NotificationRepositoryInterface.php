<?php

namespace App\Repositories\Contracts;

use App\DTO\Notification\NotificationData;
use App\DTO\Notification\NotificationFilterDTO;
use App\Enums\NotificationStatus;
use App\Models\Notification;
use App\DTO\Report\ChannelStatDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

interface NotificationRepositoryInterface
{
    public function create(NotificationData $data): Notification;

    public function updateStatus(Notification $notification, NotificationStatus $status): void;

    /** @return Collection<int, Notification> */
    public function findStuck(int $minutes): Collection;

    public function getUserHistory(NotificationFilterDTO $filter): LengthAwarePaginator;

    /** @return list<ChannelStatDTO> */
    public function getStatsByPeriod(int $userId, Carbon $from, Carbon $to): array;
}
