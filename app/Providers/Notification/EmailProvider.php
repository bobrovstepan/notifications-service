<?php

declare(strict_types=1);

namespace App\Providers\Notification;

use App\Contracts\NotificationProviderInterface;
use App\DTO\NotificationPayload;
use App\DTO\ProviderResult;
use App\Exceptions\ProviderUnavailableException;
use Illuminate\Support\Facades\Log;

class EmailProvider implements NotificationProviderInterface
{
    private const FAILURE_RATE = 0.05;

    private const MIN_DELAY_US = 100_000;

    private const MAX_DELAY_US = 300_000;

    public function send(NotificationPayload $payload): ProviderResult
    {
        Log::info('[EmailProvider] Sending email', [
            'recipient_id' => $payload->recipientId,
            'notification_id' => $payload->notificationId,
            'message_preview' => mb_substr($payload->message, 0, 50),
        ]);

        $this->simulateNetworkDelay();

        if ($this->shouldFail()) {
            Log::warning('[EmailProvider] SMTP server unavailable', [
                'recipient_id' => $payload->recipientId,
            ]);

            throw new ProviderUnavailableException('SMTP server');
        }

        if ($this->isInvalidRecipient($payload->recipientId)) {
            return ProviderResult::discarded(
                "Email address for recipient '{$payload->recipientId}' does not exist"
            );
        }

        Log::info('[EmailProvider] Email delivered', [
            'recipient_id' => $payload->recipientId,
            'notification_id' => $payload->notificationId,
        ]);

        return ProviderResult::delivered();
    }

    public function supports(string $channel): bool
    {
        return $channel === 'email';
    }

    private function simulateNetworkDelay(): void
    {
        usleep(random_int(self::MIN_DELAY_US, self::MAX_DELAY_US));
    }

    private function shouldFail(): bool
    {
        return (random_int(1, 100) / 100) < self::FAILURE_RATE;
    }

    private function isInvalidRecipient(string $recipientId): bool
    {
        return str_ends_with($recipientId, '000');
    }
}
