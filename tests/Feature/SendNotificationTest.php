<?php

declare(strict_types=1);

use App\Enums\NotificationStatus;
use App\Jobs\ProcessNotificationJob;
use Illuminate\Support\Facades\Queue;

describe('POST /api/v1/notifications', function () {

    it('creates a notification and queues recipients', function () {
        Queue::fake();

        $this->postJson('/api/v1/notifications', [
            'channel' => 'sms',
            'type' => 'transactional',
            'message' => 'Your confirmation code: 1234',
            'subscriber_ids' => ['sub_1', 'sub_2', 'sub_3'],
        ])
            ->assertStatus(202)
            ->assertJsonStructure([
                'id',
                'channel' => ['code', 'label'],
                'type' => ['value', 'label'],
                'message',
                'created_at',
            ]);

        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseCount('notification_recipients', 3);
        $this->assertDatabaseHas('notification_recipients', [
            'subscriber_id' => 'sub_1',
            'status' => NotificationStatus::Queued->value,
        ]);

        Queue::assertPushedOn('notifications.transactional', ProcessNotificationJob::class);
        Queue::assertPushed(ProcessNotificationJob::class, 3);
    });

    it('puts marketing notifications into the correct queue', function () {
        Queue::fake();

        $this->postJson('/api/v1/notifications', [
            'channel' => 'email',
            'type' => 'marketing',
            'message' => '50% off today only!',
            'subscriber_ids' => ['sub_1', 'sub_2'],
        ]);

        Queue::assertPushedOn('notifications.marketing', ProcessNotificationJob::class);
    });

    it('deduplicates subscribers within a single request', function () {
        Queue::fake();

        $this->postJson('/api/v1/notifications', [
            'channel' => 'sms',
            'type' => 'transactional',
            'message' => 'Test',
            'subscriber_ids' => ['sub_1', 'sub_1', 'sub_2'],
        ]);

        $this->assertDatabaseCount('notification_recipients', 2);
        Queue::assertPushed(ProcessNotificationJob::class, 2);
    });

    it('returns 422 for invalid channel', function () {
        $this->postJson('/api/v1/notifications', [
            'channel' => 'telegram',
            'type' => 'transactional',
            'message' => 'Test',
            'subscriber_ids' => ['sub_1'],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['channel']);
    });

    it('returns 422 when subscriber_ids is empty', function () {
        $this->postJson('/api/v1/notifications', [
            'channel' => 'sms',
            'type' => 'transactional',
            'message' => 'Test',
            'subscriber_ids' => [],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['subscriber_ids']);
    });

    it('returns 422 when subscriber_ids exceeds 1000', function () {
        $this->postJson('/api/v1/notifications', [
            'channel' => 'sms',
            'type' => 'transactional',
            'message' => 'Test',
            'subscriber_ids' => array_map(fn ($i) => "sub_{$i}", range(1, 1001)),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['subscriber_ids']);
    });

});

describe('Idempotency', function () {

    it('returns cached response on repeated request with same key', function () {
        Queue::fake();

        $payload = [
            'channel' => 'sms',
            'type' => 'transactional',
            'message' => 'Code: 9999',
            'subscriber_ids' => ['sub_1'],
        ];

        $headers = ['X-Idempotency-Key' => 'unique-key-abc123'];

        $this->postJson('/api/v1/notifications', $payload, $headers)
            ->assertStatus(202);

        $this->postJson('/api/v1/notifications', $payload, $headers)
            ->assertOk()
            ->assertHeader('X-Idempotent-Replayed', 'true');

        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseCount('notification_recipients', 1);
        Queue::assertPushed(ProcessNotificationJob::class, 1);
    });

    it('creates a new notification when keys differ', function () {
        Queue::fake();

        $payload = [
            'channel' => 'sms',
            'type' => 'transactional',
            'message' => 'Code: 9999',
            'subscriber_ids' => ['sub_1'],
        ];

        $this->postJson('/api/v1/notifications', $payload, ['X-Idempotency-Key' => 'key-1']);
        $this->postJson('/api/v1/notifications', $payload, ['X-Idempotency-Key' => 'key-2']);

        $this->assertDatabaseCount('notifications', 2);
        Queue::assertPushed(ProcessNotificationJob::class, 2);
    });

});
