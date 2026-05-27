<?php

declare(strict_types=1);

namespace App\Channels\Contracts;

use App\Models\Notification;

interface ChannelHandlerInterface
{
    public function send(Notification $notification): void;
}
