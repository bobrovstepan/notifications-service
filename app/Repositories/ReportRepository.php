<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Report\ChannelStatDTO;
use App\DTO\Report\ReportData;
use App\Enums\NotificationStatus;
use App\Enums\ReportStatus;
use App\Models\Notification;
use App\Models\Report;
use App\Repositories\Contracts\ReportRepositoryInterface;
use Illuminate\Support\Carbon;

class ReportRepository implements ReportRepositoryInterface
{
    public function create(ReportData $data): Report
    {
        return Report::create($data->toArray());
    }

    public function updateStatus(Report $report, ReportStatus $status): void
    {
        $report->update([Report::FIELD_STATUS => $status]);
    }

    public function updateFilePath(Report $report, string $filename): void
    {
        $report->update([Report::FIELD_FILE_PATH => $filename]);
    }

    /** @return list<ChannelStatDTO> */
    public function getStatsByPeriod(int $userId, Carbon $from, Carbon $to): array
    {
        return Notification::query()
            ->where(Notification::FIELD_USER_ID, $userId)
            ->whereBetween('created_at', [$from, $to])
            ->join('channels', 'notifications.channel_id', '=', 'channels.id')
            ->selectRaw('channels.name as channel, COUNT(*) as total, SUM(CASE WHEN notifications.status = ? THEN 1 ELSE 0 END) as errors', [NotificationStatus::Error->value])
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
