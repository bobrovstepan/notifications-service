<?php

declare(strict_types=1);

namespace App\Channels\Handlers;

use App\Channels\Contracts\ChannelHandlerInterface;
use App\DTO\Notification\SendNotificationDTO;
use Illuminate\Support\Facades\Log;

class TelegramChannelHandler implements ChannelHandlerInterface
{
    public function send(SendNotificationDTO $dto): void
    {
        Log::info('Sending telegram notification', [
            'notification_id' => $dto->notificationId,
            'chat_id' => $dto->recipient,
        ]);
    }
}
