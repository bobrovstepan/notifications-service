<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Support\Str;

describe('GET /api/v1/notifications/{id}', function () {

    it('returns notification status with recipients breakdown', function () {
        $notification = Notification::factory()
            ->forChannel(Channel::where('code', 'sms')->first())
            ->transactional()
            ->create();

        NotificationRecipient::factory()->forNotification($notification)->queued()->create();
        NotificationRecipient::factory()->forNotification($notification)->delivered()->create();
        NotificationRecipient::factory()->forNotification($notification)->discarded()->create();

        $this->getJson("/api/v1/notifications/{$notification->id}")
            ->assertOk()
            ->assertJsonStructure([
                'id', 'channel', 'type', 'message',
                'recipients' => ['total', 'queued', 'sent', 'delivered', 'discarded'],
                'created_at',
            ])
            ->assertJsonPath('recipients.total', 3)
            ->assertJsonPath('recipients.delivered', 1)
            ->assertJsonPath('recipients.discarded', 1);
    });

    it('returns 404 for non-existent notification', function () {
        $this->getJson('/api/v1/notifications/'.Str::uuid())
            ->assertNotFound();
    });

});

describe('GET /api/v1/subscribers/{id}/notifications', function () {

    it('returns subscriber notification history', function () {
        $channel = Channel::where('code', 'email')->first();

        $notification1 = Notification::factory()->forChannel($channel)->marketing()->create();
        $notification2 = Notification::factory()->forChannel($channel)->transactional()->create();

        NotificationRecipient::factory()->forNotification($notification1)->forSubscriber('sub_42')->delivered()->create();
        NotificationRecipient::factory()->forNotification($notification2)->forSubscriber('sub_42')->sent()->create();
        NotificationRecipient::factory()->forNotification($notification1)->forSubscriber('sub_99')->delivered()->create();

        $this->getJson('/api/v1/subscribers/sub_42/notifications')
            ->assertOk()
            ->assertJsonCount(2);
    });

    it('filters by status', function () {
        $notification = Notification::factory()
            ->forChannel(Channel::where('code', 'sms')->first())
            ->transactional()
            ->create();

        NotificationRecipient::factory()->forNotification($notification)->forSubscriber('sub_5')->delivered()->create();
        NotificationRecipient::factory()->forNotification($notification)->forSubscriber('sub_6')->discarded()->create();

        $this->getJson('/api/v1/subscribers/sub_5/notifications?status=delivered')
            ->assertOk()
            ->assertJsonCount(1);
    });

});
