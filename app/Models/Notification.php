<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property int $channel_id
 * @property NotificationType $type
 * @property string $message
 * @property string|null $idempotency_key
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Channel     $channel
 */
class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'channel_id',
        'type',
        'message',
        'idempotency_key',
    ];

    protected $casts = [
        'type' => NotificationType::class,
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(NotificationRecipient::class);
    }

    public function scopeTransactional($query): void
    {
        $query->where('type', NotificationType::Transactional);
    }

    public function scopeMarketing($query): void
    {
        $query->where('type', NotificationType::Marketing);
    }
}
