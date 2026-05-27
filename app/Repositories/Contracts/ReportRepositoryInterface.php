<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Report\ReportData;
use App\Enums\ReportStatus;
use App\Models\Report;

interface ReportRepositoryInterface
{
    public function create(ReportData $data): Report;

    public function updateStatus(Report $report, ReportStatus $status): void;

    public function updateFilePath(Report $report, string $filename): void;
}
