<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\NotificationStatus;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Notification */
class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'channel' => $this->formatChannel(),
            'type' => $this->formatType(),
            'message' => $this->message,
            'recipients' => $this->whenLoaded('recipients', fn () => $this->formatRecipients()),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }

    private function formatChannel(): array
    {
        return [
            'code' => $this->channel->code->value,
            'label' => $this->channel->code->label(),
        ];
    }

    private function formatType(): array
    {
        return [
            'value' => $this->type->value,
            'label' => $this->type->label(),
        ];
    }

    private function formatRecipients(): array
    {
        /** @var Collection<int, NotificationRecipient> $recipients */
        $recipients = $this->recipients;

        return [
            'total' => $recipients->count(),
            'queued' => $recipients->filter(fn (NotificationRecipient $r) => $r->status === NotificationStatus::Queued)->count(),
            'sent' => $recipients->filter(fn (NotificationRecipient $r) => $r->status === NotificationStatus::Sent)->count(),
            'delivered' => $recipients->filter(fn (NotificationRecipient $r) => $r->status === NotificationStatus::Delivered)->count(),
            'discarded' => $recipients->filter(fn (NotificationRecipient $r) => $r->status === NotificationStatus::Discarded)->count(),
        ];
    }
}
