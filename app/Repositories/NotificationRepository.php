<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\DTO\SendNotificationDTO;
use App\Enums\NotificationChannel;
use App\Models\Channel;
use App\Models\IdempotencyKey;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Carbon\CarbonInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function findWithRelations(string $id): Notification
    {
        return Notification::with('channel', 'recipients')
            ->findOrFail($id);
    }

    public function findActiveChannel(NotificationChannel $channel): Channel
    {
        return Channel::where('code', $channel->value)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function createNotification(SendNotificationDTO $dto, int $channelId): Notification
    {
        return Notification::create([
            'channel_id' => $channelId,
            'type' => $dto->type,
            'message' => $dto->message,
            'idempotency_key' => $dto->idempotencyKey,
        ]);
    }

    public function insertRecipients(array $recipients): void
    {
        NotificationRecipient::insert($recipients);
    }

    public function findIdempotencyKey(string $key): ?IdempotencyKey
    {
        return IdempotencyKey::find($key);
    }

    public function deleteIdempotencyKey(string $key): void
    {
        IdempotencyKey::destroy($key);
    }

    public function upsertIdempotencyKey(
        string $key,
        string $notificationId,
        array $response,
        CarbonInterface $expiresAt,
    ): void {
        IdempotencyKey::updateOrCreate(
            ['key' => $key],
            [
                'notification_id' => $notificationId,
                'response_snapshot' => $response,
                'expires_at' => $expiresAt,
            ],
        );
    }
}
