<?php

namespace App\DTO\Report;

use App\Models\Report;
use Carbon\Carbon;

final class ReportData
{
    public function __construct(
        public readonly int    $userId,
        public readonly Carbon $periodFrom,
        public readonly Carbon $periodTo,
    ) {}

    public function toArray(): array
    {
        return [
            Report::FIELD_USER_ID     => $this->userId,
            Report::FIELD_PERIOD_FROM => $this->periodFrom,
            Report::FIELD_PERIOD_TO   => $this->periodTo,
        ];
    }
}
