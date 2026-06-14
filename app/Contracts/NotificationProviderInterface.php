<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTO\NotificationPayload;
use App\DTO\ProviderResult;

interface NotificationProviderInterface
{
    public function send(NotificationPayload $payload): ProviderResult;

    public function supports(string $channel): bool;
}
