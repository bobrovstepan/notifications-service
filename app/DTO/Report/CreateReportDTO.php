<?php

declare(strict_types=1);

namespace App\DTO\Report;

use App\Http\Requests\Report\StoreReportRequest;
use Carbon\Carbon;

final class CreateReportDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly Carbon $periodFrom,
        public readonly Carbon $periodTo,
    ) {}

    public static function fromRequest(StoreReportRequest $request): self
    {
        return new self(
            userId: $request->integer('user_id'),
            periodFrom: $request->filled('period_from')
                ? Carbon::parse($request->input('period_from'))
                : Carbon::createFromTimestamp(0),
            periodTo: $request->filled('period_to')
                ? Carbon::parse($request->input('period_to'))
                : Carbon::now(),
        );
    }
}
