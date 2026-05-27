<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'period_from' => $this->period_from->toDateString(),
            'period_to'   => $this->period_to->toDateString(),
            'status'      => $this->status,
            'created_at'  => $this->created_at,
        ];
    }
}
