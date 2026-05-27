<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 60];

    public function __construct(public readonly Report $report) {}

    public function handle(ReportService $reportService): void
    {
        $reportService->updateStatus($this->report, ReportStatus::Processing);

        $filename = $reportService->generate($this->report);

        $reportService->markAsReady($this->report, $filename);
    }

    public function failed(ReportService $reportService, Throwable $e): void
    {
        $reportService->updateStatus($this->report, ReportStatus::Failed);
    }
}
