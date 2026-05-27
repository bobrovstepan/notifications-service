<?php

declare(strict_types=1);

use App\Enums\NotificationStatus;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;

test('marks notification as error on failure', function () {
    $notification = Mockery::mock(Notification::class);

    $repository = Mockery::mock(NotificationRepositoryInterface::class);
    $repository->shouldReceive('updateStatus')
        ->once()
        ->with($notification, NotificationStatus::Error);

    $job = new SendNotificationJob($notification);
    $job->failed($repository, new RuntimeException('fail'));
});
