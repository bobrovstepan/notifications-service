<?php

namespace App\Jobs;

use App\Channels\Factories\NotificationChannelFactory;
use App\Enums\NotificationStatus;
use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 60];

    public function __construct(public readonly Notification $notification) {}

    public function handle(
        NotificationChannelFactory      $factory,
        NotificationRepositoryInterface $repository,
    ): void {
        $this->notification->loadMissing('channel');

        $factory->make($this->notification->channel->name->value)
            ->send($this->notification);

        $repository->updateStatus($this->notification, NotificationStatus::Sent);
    }

    public function failed(
        NotificationRepositoryInterface $repository,
        Throwable $e,
    ): void {
        $repository->updateStatus($this->notification, NotificationStatus::Error);
    }
}
