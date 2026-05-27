<?php

namespace App\Services;

use App\DTO\Report\CreateReportDTO;
use App\DTO\Report\ReportData;
use App\Enums\ReportStatus;
use App\Events\ReportRequested;
use App\Models\Report;
use App\Reports\Contracts\ReportGeneratorInterface;
use App\Repositories\Contracts\ReportRepositoryInterface;

class ReportService
{
    public function __construct(
        private readonly ReportRepositoryInterface $reportRepository,
        private readonly ReportGeneratorInterface $reportGenerator,
    ) {}

    public function create(CreateReportDTO $dto): Report
    {
        $report = $this->reportRepository->create(new ReportData(
            userId:     $dto->userId,
            periodFrom: $dto->periodFrom,
            periodTo:   $dto->periodTo,
        ));

        ReportRequested::dispatch($report);

        return $report;
    }

    public function updateStatus(Report $report, ReportStatus $status): void
    {
        $this->reportRepository->updateStatus($report, $status);
    }

    public function markAsReady(Report $report, string $filename): void
    {
        $this->reportRepository->updateFilePath($report, $filename);
        $this->reportRepository->updateStatus($report, ReportStatus::Ready);
    }

    public function generate(Report $report): string
    {
        return $this->reportGenerator->generate($report);
    }
}
