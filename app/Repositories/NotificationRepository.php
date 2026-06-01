<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Notification\NotificationData;
use App\DTO\Notification\NotificationFilterDTO;
use App\Enums\NotificationStatus;
use App\Filters\NotificationQueryFilter;
use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(private readonly NotificationQueryFilter $filter) {}

    public function create(NotificationData $data): Notification
    {
        return Notification::create($data->toArray());
    }

    public function updateStatus(Notification $notification, NotificationStatus $status): void
    {
        $notification->update([Notification::FIELD_STATUS => $status]);
    }

    public function findStuck(int $minutes): Collection
    {
        return Notification::where(Notification::FIELD_STATUS, NotificationStatus::Processing)
            ->where('updated_at', '<=', now()->subMinutes($minutes))
            ->get();
    }

    public function getUserHistory(NotificationFilterDTO $filter): LengthAwarePaginator
    {
        $query = Notification::with('channel')->where(Notification::FIELD_USER_ID, $filter->userId);

        return $this->filter->apply($query, $filter)
            ->latest()
            ->paginate($filter->perPage);
    }
}
