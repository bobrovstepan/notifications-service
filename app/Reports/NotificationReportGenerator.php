<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\Report;
use App\Reports\Contracts\ReportGeneratorInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Writer;

class NotificationReportGenerator implements ReportGeneratorInterface
{
    public function __construct(private readonly NotificationRepositoryInterface $notificationRepository) {}

    public function generate(Report $report): string
    {
        $data = $this->collectData($report);

        $csv = Writer::fromString();
        $csv->insertOne(['channel', 'total', 'errors']);

        foreach ($data as $stat) {
            $csv->insertOne([$stat->channel, $stat->total, $stat->errors]);
        }

        $filename = $this->generateFilename();
        Storage::disk('reports')->put($filename, $csv->toString());

        return $filename;
    }

    private function collectData(Report $report): array
    {
        return $this->notificationRepository->getStatsByPeriod(
            $report->user_id,
            Carbon::parse($report->period_from),
            Carbon::parse($report->period_to),
        );
    }

    private function generateFilename(): string
    {
        return Str::uuid().'.csv';
    }
}
