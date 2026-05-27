<?php

declare(strict_types=1);

use App\Enums\ReportStatus;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use Illuminate\Support\Facades\Queue;

test('creates report and returns 201', function () {
    Queue::fake();

    $this->postJson(route('api.notifications.reports.store'), [
        'user_id'     => 1,
        'period_from' => '2026-01-01',
        'period_to'   => '2026-05-27',
    ])
        ->assertStatus(201)
        ->assertJsonPath('data.status', ReportStatus::Pending->value);

    Queue::assertPushed(GenerateReportJob::class);
});

test('show returns report status', function () {
    $report = Report::factory()->create();

    $this->getJson(route('api.notifications.reports.show', $report))
        ->assertStatus(200)
        ->assertJsonPath('data.id', $report->id);
});

test('download returns 409 when report is not ready', function () {
    $report = Report::factory()->create();

    $this->getJson(route('api.notifications.reports.download', $report))
        ->assertStatus(409);
});
