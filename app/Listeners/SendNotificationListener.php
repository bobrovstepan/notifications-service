<?php

namespace App\Listeners;

use App\Events\NotificationCreated;
use App\Jobs\SendNotificationJob;

class SendNotificationListener
{
    public function handle(NotificationCreated $event): void
    {
        SendNotificationJob::dispatch($event->notification);
    }
}
