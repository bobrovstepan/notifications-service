<?php

namespace App\Channels\Contracts;

use App\Models\Notification;

interface ChannelHandlerInterface
{
    public function send(Notification $notification): void;
}
