<?php

namespace App\DTO\Report;

class ChannelStatDTO
{
    public function __construct(
        public readonly string $channel,
        public readonly int    $total,
        public readonly int    $errors,
    ) {}
}
