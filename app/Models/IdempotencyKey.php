<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdempotencyKey extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'notification_id',
        'response_snapshot',
        'expires_at',
    ];

    protected $casts = [
        'response_snapshot' => 'array',
        'expires_at' => 'datetime',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
