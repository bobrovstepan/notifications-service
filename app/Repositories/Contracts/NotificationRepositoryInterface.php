<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Notification\NotificationData;
use App\DTO\Notification\NotificationFilterDTO;
use App\Enums\NotificationStatus;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    public function create(NotificationData $data): Notification;

    public function updateStatus(Notification $notification, NotificationStatus $status): void;

    /** @return Collection<int, Notification> */
    public function findStuck(int $minutes): Collection;

    public function getUserHistory(NotificationFilterDTO $filter): LengthAwarePaginator;
}
