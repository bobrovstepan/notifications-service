<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\NotificationPayload;
use App\Enums\NotificationStatus;
use App\Exceptions\LockAcquisitionException;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationProcessingService
{
    public function __construct(
        private readonly ProviderFactory $providerFactory,
    ) {}

    public function process(string $recipientId, string $notificationId): void
    {
        $recipient = $this->findRecipient($recipientId);

        if ($recipient->status->isFinal()) {
            Log::info('Skipping duplicate job — status is already final', [
                'recipient_id' => $recipientId,
                'status' => $recipient->status->value,
            ]);

            return;
        }

        $this->processWithLock($recipient, $notificationId);
    }

    public function markAsDiscarded(string $recipientId, string $reason): void
    {
        $recipient = NotificationRecipient::find($recipientId);

        if ($recipient && ! $recipient->status->isFinal()) {
            $recipient->transitionTo(NotificationStatus::Discarded, $reason);
        }
    }

    private function processWithLock(NotificationRecipient $recipient, string $notificationId): void
    {
        $lock = Cache::lock("recipient_lock:{$recipient->id}", 60);

        if (! $lock->get()) {
            throw new LockAcquisitionException($recipient->id);
        }

        try {
            $this->send($recipient, $notificationId);
        } finally {
            $lock->forceRelease();
        }
    }

    private function send(NotificationRecipient $recipient, string $notificationId): void
    {
        /** @var Notification $notification */
        $notification = $recipient->notification;
        $channel = $notification->channel->code;
        $provider = $this->providerFactory->make($channel);
        $payload = $this->buildPayload($recipient, $notificationId);

        $recipient->transitionTo(NotificationStatus::Sent);

        $result = $provider->send($payload);

        $recipient->transitionTo($result->status, $result->failureReason);

        Log::info('Notification processed', [
            'recipient_id' => $recipient->id,
            'status' => $result->status->value,
        ]);
    }

    private function buildPayload(NotificationRecipient $recipient, string $notificationId): NotificationPayload
    {
        /** @var Notification $notification */
        $notification = $recipient->notification;

        return new NotificationPayload(
            recipientId: $recipient->subscriber_id,
            message: $notification->message,
            channel: $notification->channel->code,
            notificationId: $notificationId,
        );
    }

    private function findRecipient(string $recipientId): NotificationRecipient
    {
        return NotificationRecipient::with('notification.channel')
            ->findOrFail($recipientId);
    }
}
