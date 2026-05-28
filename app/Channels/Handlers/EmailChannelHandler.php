<?php

declare(strict_types=1);

namespace App\Channels\Handlers;

use App\Channels\Contracts\ChannelHandlerInterface;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class EmailChannelHandler implements ChannelHandlerInterface
{
    public function send(Notification $notification): void
    {
        Log::info('Sending email notification', [
            'notification_id' => $notification->id,
            'recipient' => $notification->recipient,
        ]);
    }
}
