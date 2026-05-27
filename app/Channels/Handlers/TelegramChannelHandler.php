<?php

namespace App\Channels\Handlers;

use App\Channels\Contracts\ChannelHandlerInterface;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class TelegramChannelHandler implements ChannelHandlerInterface
{
    public function send(Notification $notification): void
    {
        Log::info('Sending telegram notification', [
            'notification_id' => $notification->id,
            'chat_id'         => $notification->recipient,
        ]);
    }
}
