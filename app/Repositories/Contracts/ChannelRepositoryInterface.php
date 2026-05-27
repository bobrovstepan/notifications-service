<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Channel;

interface ChannelRepositoryInterface
{
    public function findByNameOrFail(string $name): Channel;
}
