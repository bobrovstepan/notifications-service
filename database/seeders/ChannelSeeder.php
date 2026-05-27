<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ChannelName;
use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ChannelName::cases() as $channel) {
            Channel::firstOrCreate(['name' => $channel->value]);
        }
    }
}
