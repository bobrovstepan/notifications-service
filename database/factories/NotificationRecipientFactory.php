<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\NotificationStatus;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationRecipientFactory extends Factory
{
    protected $model = NotificationRecipient::class;

    public function definition(): array
    {
        return [
            'notification_id' => NotificationFactory::new()->create()->id,
            'subscriber_id' => 'sub_'.$this->faker->unique()->numberBetween(1, 99999),
            'status' => NotificationStatus::Queued,
            'failure_reason' => null,
            'sent_at' => null,
            'delivered_at' => null,
        ];
    }

    public function queued(): static
    {
        return $this->state([
            'status' => NotificationStatus::Queued,
            'sent_at' => null,
            'delivered_at' => null,
        ]);
    }

    public function sent(): static
    {
        return $this->state([
            'status' => NotificationStatus::Sent,
            'sent_at' => now(),
        ]);
    }

    public function delivered(): static
    {
        return $this->state([
            'status' => NotificationStatus::Delivered,
            'sent_at' => now()->subMinutes(2),
            'delivered_at' => now(),
        ]);
    }

    public function discarded(string $reason = 'Recipient does not exist'): static
    {
        return $this->state([
            'status' => NotificationStatus::Discarded,
            'failure_reason' => $reason,
        ]);
    }

    public function forNotification(Notification $notification): static
    {
        return $this->state(['notification_id' => $notification->id]);
    }

    public function forSubscriber(string $subscriberId): static
    {
        return $this->state(['subscriber_id' => $subscriberId]);
    }
}
