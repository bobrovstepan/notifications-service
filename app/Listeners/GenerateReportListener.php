<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ReportRequested;
use App\Jobs\GenerateReportJob;

class GenerateReportListener
{
    public function handle(ReportRequested $event): void
    {
        GenerateReportJob::dispatch($event->report);
    }
}
