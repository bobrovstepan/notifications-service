<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\NotificationProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 5;

    public int $timeout = 30;

    public bool $failOnTimeout = false;

    public function __construct(
        private readonly string $notificationId,
        private readonly string $recipientId,
    ) {}

    public function handle(NotificationProcessingService $processingService): void
    {
        $processingService->process($this->recipientId, $this->notificationId);
    }

    public function failed(Throwable $e): void
    {
        Log::critical('Job permanently failed', [
            'notification_id' => $this->notificationId,
            'recipient_id' => $this->recipientId,
            'error' => $e->getMessage(),
        ]);

        app(NotificationProcessingService::class)->markAsDiscarded(
            recipientId: $this->recipientId,
            reason: "Job permanently failed: {$e->getMessage()}",
        );
    }

    public function backoff(): array
    {
        return [5, 25, 125, 300, 300];
    }
}
