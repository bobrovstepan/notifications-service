<?php

namespace App\Repositories\Contracts;

use App\Models\Channel;

interface ChannelRepositoryInterface
{
    public function findByNameOrFail(string $name): Channel;
}
