<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Notification\NotificationData;
use App\DTO\Notification\NotificationFilterDTO;
use App\DTO\Report\ChannelStatDTO;
use App\Enums\NotificationStatus;
use App\Filters\NotificationQueryFilter;
use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

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

    /** @return list<ChannelStatDTO> */
    public function getStatsByPeriod(int $userId, Carbon $from, Carbon $to): array
    {
        return Notification::query()
            ->where(Notification::FIELD_USER_ID, $userId)
            ->whereBetween('created_at', [$from, $to])
            ->join('channels', 'notifications.channel_id', '=', 'channels.id')
            ->selectRaw('channels.name as channel, COUNT(*) as total, SUM(CASE WHEN notifications.status = ? THEN 1 ELSE 0 END) as errors', ['error'])
            ->groupBy('channels.name')
            ->toBase()
            ->get()
            ->map(fn ($row) => new ChannelStatDTO(
                channel: $row->channel,
                total: (int) $row->total,
                errors: (int) $row->errors,
            ))
            ->all();
    }
}
