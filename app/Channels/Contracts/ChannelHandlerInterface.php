<?php

declare(strict_types=1);

namespace App\Channels\Contracts;

use App\DTO\Notification\SendNotificationDTO;

interface ChannelHandlerInterface
{
    public function send(SendNotificationDTO $dto): void;
}
