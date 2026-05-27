<?php

declare(strict_types=1);

use App\Enums\ReportStatus;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use App\Services\ReportService;

test('marks report as failed on error', function () {
    $report = Mockery::mock(Report::class);

    $service = Mockery::mock(ReportService::class);
    $service->shouldReceive('updateStatus')
        ->once()
        ->with($report, ReportStatus::Failed);

    $job = new GenerateReportJob($report);
    $job->failed($service, new RuntimeException('fail'));
});
