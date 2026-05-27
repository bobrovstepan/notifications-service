<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\NotificationStatus;
use App\Models\Channel;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id'    => 1,
            'channel_id' => Channel::factory(),
            'recipient'  => $this->faker->email(),
            'message'    => $this->faker->sentence(),
            'status'     => NotificationStatus::Processing,
        ];
    }

    public function sent(): static
    {
        return $this->state(['status' => NotificationStatus::Sent]);
    }

    public function error(): static
    {
        return $this->state(['status' => NotificationStatus::Error]);
    }
}
