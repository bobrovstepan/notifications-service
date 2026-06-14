<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\DTO\SendNotificationDTO;
use App\Enums\NotificationStatus;
use App\Jobs\ProcessNotificationJob;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotificationService
{
    private const IDEMPOTENCY_TTL_HOURS = 24;

    public function __construct(
        private readonly NotificationRepositoryInterface $notificationRepository,
    ) {}

    public function dispatch(SendNotificationDTO $dto): Notification
    {
        return DB::transaction(function () use ($dto) {
            $channel = $this->notificationRepository->findActiveChannel($dto->channel);
            $notification = $this->notificationRepository->createNotification($dto, $channel->id);
            $recipients = $this->buildRecipients($notification->id, $dto->subscriberIds);

            $this->notificationRepository->insertRecipients($recipients);
            $this->dispatchJobs($recipients, $dto);

            Log::info('Notification dispatched', [
                'notification_id' => $notification->id,
                'channel' => $dto->channel->value,
                'type' => $dto->type->value,
                'recipients_count' => count($recipients),
            ]);

            return $notification;
        });
    }

    public function findWithDetails(string $id): Notification
    {
        return $this->notificationRepository->findWithRelations($id);
    }

    public function findCachedResponse(string $idempotencyKey): ?array
    {
        $record = $this->notificationRepository->findIdempotencyKey($idempotencyKey);

        if (! $record) {
            return null;
        }

        if ($record->isExpired()) {
            $this->notificationRepository->deleteIdempotencyKey($idempotencyKey);

            return null;
        }

        return $record->response_snapshot;
    }

    public function cacheResponse(string $idempotencyKey, Notification $notification, array $response): void
    {
        $this->notificationRepository->upsertIdempotencyKey(
            key: $idempotencyKey,
            notificationId: $notification->id,
            response: $response,
            expiresAt: Carbon::now()->addHours(self::IDEMPOTENCY_TTL_HOURS),
        );
    }

    private function buildRecipients(string $notificationId, array $subscriberIds): array
    {
        return array_map(
            fn (string $subscriberId) => [
                'id' => Str::uuid()->toString(),
                'notification_id' => $notificationId,
                'subscriber_id' => $subscriberId,
                'status' => NotificationStatus::Queued->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            array_unique($subscriberIds),
        );
    }

    private function dispatchJobs(array $recipients, SendNotificationDTO $dto): void
    {
        foreach ($recipients as $recipient) {
            ProcessNotificationJob::dispatch(
                notificationId: $recipient['notification_id'],
                recipientId: $recipient['id'],
            )->onQueue($dto->type->queueName());
        }
    }
}
