<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Notification */
class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'user_id'   => $this->user_id,
            'channel'   => $this->whenLoaded('channel', fn() => $this->channel->name),
            'recipient' => $this->recipient,
            'message'   => $this->message,
            'status'    => $this->status->value,
            'created_at' => $this->created_at,
        ];
    }
}
