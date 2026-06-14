<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class LockAcquisitionException extends RuntimeException
{
    public function __construct(string $recipientId)
    {
        parent::__construct(
            "Could not acquire lock for recipient '{$recipientId}'"
        );
    }
}
