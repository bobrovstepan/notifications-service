<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class ProviderUnavailableException extends RuntimeException
{
    public function __construct(string $provider)
    {
        parent::__construct("{$provider} temporarily unavailable");
    }
}
