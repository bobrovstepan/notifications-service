<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            ['code' => 'sms',   'name' => 'SMS',   'is_active' => true],
            ['code' => 'email', 'name' => 'Email', 'is_active' => true],
        ];

        foreach ($channels as $channel) {
            Channel::updateOrCreate(
                ['code' => $channel['code']],
                $channel,
            );
        }
    }
}
