<?php

declare(strict_types=1);

use App\Contracts\NotificationProviderInterface;
use App\DTO\ProviderResult;
use App\Enums\NotificationStatus;
use App\Jobs\ProcessNotificationJob;
use App\Models\Channel;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Services\ProviderFactory;
use Illuminate\Database\UniqueConstraintViolationException;

function makeRecipient(string $channelCode = 'sms', string $subscriberId = 'sub_1'): NotificationRecipient
{
    $channel = Channel::where('code', $channelCode)->first();

    $notification = Notification::factory()
        ->forChannel($channel)
        ->transactional()
        ->create();

    return NotificationRecipient::factory()
        ->forNotification($notification)
        ->forSubscriber($subscriberId)
        ->queued()
        ->create();
}

function mockProvider(ProviderResult $result): void
{
    $provider = Mockery::mock(NotificationProviderInterface::class);
    $provider->shouldReceive('supports')->andReturn(true);
    $provider->shouldReceive('send')->once()->andReturn($result);

    $factory = Mockery::mock(ProviderFactory::class);
    $factory->shouldReceive('make')->andReturn($provider);

    app()->instance(ProviderFactory::class, $factory);
}

describe('ProcessNotificationJob', function () {

    it('transitions status to delivered on successful send', function () {
        $recipient = makeRecipient();
        mockProvider(ProviderResult::delivered());

        ProcessNotificationJob::dispatchSync(
            notificationId: $recipient->notification_id,
            recipientId: $recipient->id,
        );

        expect($recipient->fresh()->status)->toBe(NotificationStatus::Delivered)
            ->and($recipient->fresh()->delivered_at)->not->toBeNull();
    });

    it('transitions status to discarded on permanent provider failure', function () {
        $recipient = makeRecipient();
        mockProvider(ProviderResult::discarded('Recipient does not exist'));

        ProcessNotificationJob::dispatchSync(
            notificationId: $recipient->notification_id,
            recipientId: $recipient->id,
        );

        expect($recipient->fresh()->status)->toBe(NotificationStatus::Discarded)
            ->and($recipient->fresh()->failure_reason)->toBe('Recipient does not exist');
    });

    it('skips job if status is already final — exactly-once', function () {
        $recipient = makeRecipient();
        $recipient->update(['status' => NotificationStatus::Delivered]);

        $provider = Mockery::mock(NotificationProviderInterface::class);
        $provider->shouldNotReceive('send');

        $factory = Mockery::mock(ProviderFactory::class);
        $factory->shouldNotReceive('make');
        app()->instance(ProviderFactory::class, $factory);

        ProcessNotificationJob::dispatchSync(
            notificationId: $recipient->notification_id,
            recipientId: $recipient->id,
        );

        expect($recipient->fresh()->status)->toBe(NotificationStatus::Delivered);
    });

    it('marks recipient as discarded when all retries are exhausted', function () {
        $recipient = makeRecipient();

        $provider = Mockery::mock(NotificationProviderInterface::class);
        $provider->shouldReceive('supports')->andReturn(true);
        $provider->shouldReceive('send')->andThrow(new RuntimeException('Gateway timeout'));

        $factory = Mockery::mock(ProviderFactory::class);
        $factory->shouldReceive('make')->andReturn($provider);
        app()->instance(ProviderFactory::class, $factory);

        $job = new ProcessNotificationJob(
            notificationId: $recipient->notification_id,
            recipientId: $recipient->id,
        );
        $job->failed(new RuntimeException('Gateway timeout'));

        expect($recipient->fresh()->status)->toBe(NotificationStatus::Discarded)
            ->and($recipient->fresh()->failure_reason)->toContain('Gateway timeout');
    });

    it('prevents duplicate recipients via unique DB constraint — exactly-once', function () {
        $notification = Notification::factory()
            ->forChannel(Channel::where('code', 'sms')->first())
            ->transactional()
            ->create();

        NotificationRecipient::factory()
            ->forNotification($notification)
            ->forSubscriber('sub_dup')
            ->queued()
            ->create();

        expect(function () use ($notification) {
            DB::transaction(function () use ($notification) {
                NotificationRecipient::factory()
                    ->forNotification($notification)
                    ->forSubscriber('sub_dup')
                    ->queued()
                    ->create();
            });
        })->toThrow(UniqueConstraintViolationException::class);

        $this->assertDatabaseCount('notification_recipients', 1);
    });

});
