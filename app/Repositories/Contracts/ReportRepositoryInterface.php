<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Report\ChannelStatDTO;
use App\DTO\Report\ReportData;
use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Support\Carbon;

interface ReportRepositoryInterface
{
    public function create(ReportData $data): Report;

    public function updateStatus(Report $report, ReportStatus $status): void;

    public function updateFilePath(Report $report, string $filename): void;

    /** @return list<ChannelStatDTO> */
    public function getStatsByPeriod(int $userId, Carbon $from, Carbon $to): array;
}
