<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportRequested
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Report $report) {}
}
