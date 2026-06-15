<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\NotificationRecipient;

interface RecipientRepositoryInterface
{
    public function findWithRelations(string $recipientId): NotificationRecipient;

    public function findById(string $recipientId): ?NotificationRecipient;
}
