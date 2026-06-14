<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Notification Service API',
    version: '1.0.0',
    description: 'Microservice for bulk SMS and Email delivery with priority queuing and guaranteed at-least-once delivery.'
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Local development'
)]
class OpenApi {}
