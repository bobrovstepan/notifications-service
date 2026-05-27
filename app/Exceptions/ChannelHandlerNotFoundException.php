<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class ChannelHandlerNotFoundException extends RuntimeException
{
    public static function forChannel(string $channelName): self
    {
        return new self(__('errors.channel_handler_missing', ['channel' => $channelName]));
    }
}
