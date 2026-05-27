<?php

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
