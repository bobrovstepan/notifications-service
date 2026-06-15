<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\RecipientRepositoryInterface;
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
        private readonly RecipientRepositoryInterface $recipientRepository,
        private readonly RecipientStatusService $statusService,
    ) {}

    public function process(string $recipientId, string $notificationId): void
    {
        $recipient = $this->recipientRepository->findWithRelations($recipientId);

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
        $recipient = $this->recipientRepository->findById($recipientId);

        if ($recipient && ! $recipient->status->isFinal()) {
            $this->statusService->transition($recipient, NotificationStatus::Discarded, $reason);
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
        $payload = $this->buildPayload($recipient, $notificationId);

        /** @var Notification $notification */
        $notification = $recipient->notification;
        $provider = $this->providerFactory->make($notification->channel->code);

        $this->statusService->transition($recipient, NotificationStatus::Sent);

        $result = $provider->send($payload);

        $this->statusService->transition($recipient, $result->status, $result->failureReason);

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
}
