<?php

declare(strict_types=1);

namespace App\Providers\Notification;

use App\Contracts\NotificationProviderInterface;
use App\DTO\NotificationPayload;
use App\DTO\ProviderResult;
use App\Exceptions\ProviderUnavailableException;
use Illuminate\Support\Facades\Log;

class SmsProvider implements NotificationProviderInterface
{
    private const FAILURE_RATE = 0.10;

    private const MIN_DELAY_US = 50_000;

    private const MAX_DELAY_US = 200_000;

    public function send(NotificationPayload $payload): ProviderResult
    {
        Log::info('[SmsProvider] Sending SMS', [
            'recipient_id' => $payload->recipientId,
            'notification_id' => $payload->notificationId,
            'message_preview' => mb_substr($payload->message, 0, 50),
        ]);

        $this->simulateNetworkDelay();

        if ($this->shouldFail()) {
            Log::warning('[SmsProvider] SMS gateway unavailable', [
                'recipient_id' => $payload->recipientId,
            ]);

            throw new ProviderUnavailableException('SMS gateway');
        }

        if ($this->isInvalidRecipient($payload->recipientId)) {
            return ProviderResult::discarded(
                "Phone number for recipient '{$payload->recipientId}' does not exist"
            );
        }

        Log::info('[SmsProvider] SMS delivered', [
            'recipient_id' => $payload->recipientId,
            'notification_id' => $payload->notificationId,
        ]);

        return ProviderResult::delivered();
    }

    public function supports(string $channel): bool
    {
        return $channel === 'sms';
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
