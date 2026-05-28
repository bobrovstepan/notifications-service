<?php

declare(strict_types=1);

use App\Enums\ChannelName;
use App\Enums\NotificationStatus;
use App\Jobs\SendNotificationJob;
use App\Models\Channel;
use App\Models\Notification;
use Illuminate\Support\Facades\Queue;

test('creates notification and dispatches job', function () {
    Queue::fake();
    Channel::factory()->create();

    $response = $this->postJson(route('api.notifications.store'), [
        'user_id' => 1,
        'channel' => ChannelName::Email->value,
        'recipient' => 'user@example.com',
        'message' => 'Hello',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.status', NotificationStatus::Processing->value);

    Queue::assertPushed(SendNotificationJob::class);
});

test('rejects message over 500 characters', function () {
    $this->postJson(route('api.notifications.store'), [
        'user_id' => 1,
        'channel' => ChannelName::Email->value,
        'recipient' => 'user@example.com',
        'message' => str_repeat('a', 501),
    ])->assertStatus(422)->assertJsonValidationErrors('message');
});

test('rejects invalid recipient for channel', function () {
    $this->postJson(route('api.notifications.store'), [
        'user_id' => 1,
        'channel' => ChannelName::Telegram->value,
        'recipient' => 'not-a-number',
        'message' => 'Hello',
    ])->assertStatus(422)->assertJsonValidationErrors('recipient');
});

test('returns notification by id', function () {
    $channel = Channel::factory()->create();
    $notification = Notification::factory()->sent()->create(['channel_id' => $channel->id]);

    $this->getJson(route('api.notifications.show', $notification))
        ->assertStatus(200)
        ->assertJsonPath('data.id', $notification->id)
        ->assertJsonPath('data.status', NotificationStatus::Sent->value);
});

test('returns 404 for unknown notification', function () {
    $this->getJson(route('api.notifications.show', 999))->assertStatus(404);
});

test('returns paginated history filtered by status', function () {
    $channel = Channel::factory()->create();
    Notification::factory()->count(3)->sent()->create(['user_id' => 1, 'channel_id' => $channel->id]);
    Notification::factory()->error()->create(['user_id' => 1, 'channel_id' => $channel->id]);

    $this->getJson(route('api.notifications.index', ['user_id' => 1, 'filter[status]' => 'sent']))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('returns paginated history filtered by channel', function () {
    $email = Channel::factory()->create(['name' => ChannelName::Email->value]);
    $telegram = Channel::factory()->telegram()->create();

    Notification::factory()->count(2)->create(['user_id' => 1, 'channel_id' => $email->id]);
    Notification::factory()->create(['user_id' => 1, 'channel_id' => $telegram->id, 'recipient' => '123456789']);

    $this->getJson(route('api.notifications.index', ['user_id' => 1, 'filter[channel]' => 'email']))
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');
});
