<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\NotificationStatus;
use App\Models\NotificationRecipient;

class RecipientStatusService
{
    public function transition(
        NotificationRecipient $recipient,
        NotificationStatus $next,
        ?string $failureReason = null,
    ): void {
        if (! $recipient->status->isTransitionAllowed($next)) {
            throw new \LogicException(
                "Transition {$recipient->status->value} → {$next->value} is not allowed for recipient {$recipient->id}"
            );
        }

        $data = ['status' => $next];

        if ($next === NotificationStatus::Sent) {
            $data['sent_at'] = now();
        }

        if ($next === NotificationStatus::Delivered) {
            $data['delivered_at'] = now();
        }

        if ($next === NotificationStatus::Discarded && $failureReason) {
            $data['failure_reason'] = $failureReason;
        }

        $recipient->update($data);
    }
}
