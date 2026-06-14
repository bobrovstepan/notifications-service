<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTO\SendNotificationDTO;
use App\Enums\NotificationChannel;
use App\Models\Channel;
use App\Models\IdempotencyKey;
use App\Models\Notification;
use Carbon\CarbonInterface;

interface NotificationRepositoryInterface
{
    public function findWithRelations(string $id): Notification;

    public function findActiveChannel(NotificationChannel $channel): Channel;

    public function createNotification(SendNotificationDTO $dto, int $channelId): Notification;

    public function insertRecipients(array $recipients): void;

    public function findIdempotencyKey(string $key): ?IdempotencyKey;

    public function deleteIdempotencyKey(string $key): void;

    public function upsertIdempotencyKey(
        string $key,
        string $notificationId,
        array $response,
        CarbonInterface $expiresAt,
    ): void;
}
