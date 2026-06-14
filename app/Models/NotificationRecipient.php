<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $notification_id
 * @property string $subscriber_id
 * @property NotificationStatus $status
 * @property string|null $failure_reason
 * @property Carbon|null $sent_at
 * @property Carbon|null $delivered_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class NotificationRecipient extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'notification_id',
        'subscriber_id',
        'status',
        'failure_reason',
        'sent_at',
        'delivered_at',
    ];

    protected $casts = [
        'status' => NotificationStatus::class,
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    public function transitionTo(NotificationStatus $next, ?string $failureReason = null): void
    {
        if (! $this->status->isTransitionAllowed($next)) {
            throw new \LogicException(
                "Transition {$this->status->value} → {$next->value} is not allowed for recipient {$this->id}"
            );
        }

        $data = ['status' => $next];

        if ($next === NotificationStatus::Sent) {
            $data['sent_at'] = now();
        }

        if ($next === NotificationStatus::Delivered) {
            $data['delivered_at'] = now();
        }

        if ($next === NotificationStatus::Discarded && $failureReason) {
            $data['failure_reason'] = $failureReason;
        }

        $this->update($data);
    }

    public function scopeQueued($query): void
    {
        $query->where('status', NotificationStatus::Queued);
    }

    public function scopeSent($query): void
    {
        $query->where('status', NotificationStatus::Sent);
    }

    public function scopeDelivered($query): void
    {
        $query->where('status', NotificationStatus::Delivered);
    }

    public function scopeDiscarded($query): void
    {
        $query->where('status', NotificationStatus::Discarded);
    }

    public function scopeForSubscriber($query, string $subscriberId): void
    {
        $query->where('subscriber_id', $subscriberId);
    }
}
