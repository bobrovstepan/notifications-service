<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Channel;
use App\Repositories\Contracts\ChannelRepositoryInterface;

class ChannelRepository implements ChannelRepositoryInterface
{
    public function findByNameOrFail(string $name): Channel
    {
        return Channel::where('name', $name)->firstOrFail();
    }
}
