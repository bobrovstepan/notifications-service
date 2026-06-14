<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\NotificationType;
use App\Models\Channel;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'channel_id' => Channel::where('code', 'sms')->first()?->id
                ?? ChannelFactory::new()->sms()->create()->id,
            'type' => NotificationType::Transactional,
            'message' => $this->faker->sentence(),
            'idempotency_key' => null,
        ];
    }

    public function transactional(): static
    {
        return $this->state(['type' => NotificationType::Transactional]);
    }

    public function marketing(): static
    {
        return $this->state(['type' => NotificationType::Marketing]);
    }

    public function withIdempotencyKey(string $key): static
    {
        return $this->state(['idempotency_key' => $key]);
    }

    public function forChannel(Channel $channel): static
    {
        return $this->state(['channel_id' => $channel->id]);
    }
}
