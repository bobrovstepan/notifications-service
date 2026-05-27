<?php

namespace Database\Factories;

use App\Enums\ChannelName;
use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    protected $model = Channel::class;

    public function definition(): array
    {
        return [
            'name' => ChannelName::Email->value,
        ];
    }

    public function telegram(): static
    {
        return $this->state(['name' => ChannelName::Telegram->value]);
    }
}
