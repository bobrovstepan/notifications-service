<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\NotificationChannel;

class ProviderNotFoundException extends \RuntimeException
{
    public function __construct(NotificationChannel $channel)
    {
        parent::__construct(
            "No provider registered for channel '{$channel->value}'"
        );
    }
}
