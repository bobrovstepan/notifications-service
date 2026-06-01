<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $channel_id
 * @property string $recipient
 * @property string $message
 * @property NotificationStatus $status
 * @property-read Channel $channel
 */
#[Fillable(['user_id', 'channel_id', 'recipient', 'message', 'status'])]
class Notification extends Model
{
    use HasFactory;

    public const string FIELD_USER_ID = 'user_id';

    public const string FIELD_CHANNEL_ID = 'channel_id';

    public const string FIELD_RECIPIENT = 'recipient';

    public const string FIELD_MESSAGE = 'message';

    public const string FIELD_STATUS = 'status';

    protected function casts(): array
    {
        return [
            'status' => NotificationStatus::class,
        ];
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
