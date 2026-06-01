<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\NotificationRetried;
use App\Jobs\SendNotificationJob;

class RetryNotificationListener
{
    public function handle(NotificationRetried $event): void
    {
        SendNotificationJob::dispatch($event->notification);
    }
}
