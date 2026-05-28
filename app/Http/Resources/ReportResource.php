<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/** @mixin Report */
class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Carbon $periodFrom */
        $periodFrom = $this->period_from;
        /** @var Carbon $periodTo */
        $periodTo = $this->period_to;
        /** @var ReportStatus $status */
        $status = $this->status;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'period_from' => $periodFrom->toDateString(),
            'period_to' => $periodTo->toDateString(),
            'status' => $status->value,
            'created_at' => $this->created_at,
        ];
    }
}
