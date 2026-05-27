<?php

namespace App\Reports\Contracts;

use App\Models\Report;

interface ReportGeneratorInterface
{
    public function generate(Report $report): string;
}
