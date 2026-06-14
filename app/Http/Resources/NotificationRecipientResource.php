<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin NotificationRecipient */
class NotificationRecipientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subscriber_id' => $this->subscriber_id,
            'status' => $this->formatStatus(),
            'failure_reason' => $this->failure_reason,
            'sent_at' => $this->sent_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'notification' => $this->whenLoaded('notification', fn () => $this->formatNotification()),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }

    private function formatStatus(): array
    {
        return [
            'value' => $this->status->value,
            'label' => $this->status->label(),
        ];
    }

    private function formatNotification(): array
    {
        /** @var Notification $notification */
        $notification = $this->notification;

        return [
            'id' => $notification->id,
            'channel' => $notification->channel->code->value,
            'type' => $notification->type->value,
            'message' => $notification->message,
        ];
    }
}
