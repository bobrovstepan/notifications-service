<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RecipientResponse',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'subscriber_id', type: 'string', example: 'user_42'),
        new OA\Property(property: 'status', type: 'object', properties: [
            new OA\Property(property: 'value', type: 'string', enum: ['queued', 'sent', 'delivered', 'discarded'], example: 'delivered'),
            new OA\Property(property: 'label', type: 'string', example: 'Delivered'),
        ]),
        new OA\Property(property: 'failure_reason', type: 'string', nullable: true),
        new OA\Property(property: 'sent_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'delivered_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'notification', type: 'object', nullable: true, properties: [
            new OA\Property(property: 'id', type: 'string', format: 'uuid'),
            new OA\Property(property: 'channel', type: 'string', example: 'sms'),
            new OA\Property(property: 'type', type: 'string', example: 'transactional'),
            new OA\Property(property: 'message', type: 'string'),
        ]),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class RecipientSchema {}
