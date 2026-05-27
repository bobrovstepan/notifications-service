<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Report\ReportData;
use App\Enums\ReportStatus;
use App\Models\Report;
use App\Repositories\Contracts\ReportRepositoryInterface;

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
}
