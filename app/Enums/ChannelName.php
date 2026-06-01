<?php

declare(strict_types=1);

namespace App\Enums;

use App\Channels\Contracts\ChannelHandlerInterface;
use App\Channels\Handlers\EmailChannelHandler;
use App\Channels\Handlers\TelegramChannelHandler;

enum ChannelName: string
{
    case Email = 'email';
    case Telegram = 'telegram';

    public function recipientRule(): string
    {
        return match ($this) {
            self::Email => 'email',
            self::Telegram => 'regex:/^\d+$/',
        };
    }

    /** @return class-string<ChannelHandlerInterface> */
    public function handlerClass(): string
    {
        return match ($this) {
            self::Email => EmailChannelHandler::class,
            self::Telegram => TelegramChannelHandler::class,
        };
    }
}
