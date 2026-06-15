<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\RecipientRepositoryInterface;
use App\Models\NotificationRecipient;

class RecipientRepository implements RecipientRepositoryInterface
{
    public function findWithRelations(string $recipientId): NotificationRecipient
    {
        return NotificationRecipient::with('notification.channel')
            ->findOrFail($recipientId);
    }

    public function findById(string $recipientId): ?NotificationRecipient
    {
        return NotificationRecipient::find($recipientId);
    }
}
