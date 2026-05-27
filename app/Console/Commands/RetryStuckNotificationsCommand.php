<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class RetryStuckNotificationsCommand extends Command
{
    protected $signature = 'notifications:retry-stuck {--minutes=10 : Minutes a notification can stay in processing before retry}';

    protected $description = 'Retry notifications stuck in processing state';

    public function __construct(private readonly NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $minutes = (int) $this->option('minutes');
        $count   = $this->notificationService->retryStuck($minutes);

        if ($count === 0) {
            $this->info(__('commands.retry_stuck.none_found'));
            return;
        }

        $this->info(__('commands.retry_stuck.retried', ['count' => $count]));
    }
}
