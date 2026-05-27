<?php

namespace App\Channels\Factories;

use App\Channels\Contracts\ChannelHandlerInterface;
use App\Exceptions\ChannelHandlerNotFoundException;

class NotificationChannelFactory
{
    /**
     * @param array<string, ChannelHandlerInterface> $handlers
     */
    public function __construct(
        private readonly array $handlers
    ) {}

    public function make(string $channelName): ChannelHandlerInterface
    {
        return $this->handlers[$channelName]
            ?? throw ChannelHandlerNotFoundException::forChannel($channelName);
    }
}
