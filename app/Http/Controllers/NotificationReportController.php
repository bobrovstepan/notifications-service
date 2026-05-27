<?php

namespace App\Http\Controllers;

use App\DTO\Report\CreateReportDTO;
use App\Enums\ReportStatus;
use App\Http\Requests\Report\StoreReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class NotificationReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function store(StoreReportRequest $request): JsonResponse
    {
        $report = $this->reportService->create(
            CreateReportDTO::fromRequest($request)
        );

        return (new ReportResource($report))
            ->additional(['message' => __('report.created')])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Report $report): JsonResponse
    {
        return (new ReportResource($report))
            ->additional(['message' => __('report.retrieved')])
            ->response();
    }

    public function download(Report $report): BinaryFileResponse|JsonResponse
    {
        if ($report->status !== ReportStatus::Ready || $report->file_path === null) {
            return response()->json(['message' => __('report.not_ready')], 409);
        }

        $filename = sprintf(
            'report_%s_%s.csv',
            $report->period_from->toDateString(),
            $report->period_to->toDateString(),
        );

        return response()->download(
            Storage::disk('reports')->path($report->file_path),
            $filename,
        );
    }
}
