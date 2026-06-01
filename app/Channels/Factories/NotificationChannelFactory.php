<?php

declare(strict_types=1);

namespace App\Channels\Factories;

use App\Channels\Contracts\ChannelHandlerInterface;
use App\Enums\ChannelName;
use App\Exceptions\ChannelHandlerNotFoundException;

class NotificationChannelFactory
{
    /**
     * @param  array<string, ChannelHandlerInterface>  $handlers
     */
    public function __construct(
        private readonly array $handlers,
    ) {}

    public function make(ChannelName $channelName): ChannelHandlerInterface
    {
        return $this->handlers[$channelName->value]
            ?? throw ChannelHandlerNotFoundException::forChannel($channelName->value);
    }
}
