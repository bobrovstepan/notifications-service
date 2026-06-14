<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NotificationResponse',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'channel', type: 'object', properties: [
            new OA\Property(property: 'code', type: 'string', enum: ['sms', 'email'], example: 'sms'),
            new OA\Property(property: 'label', type: 'string', example: 'SMS'),
        ]),
        new OA\Property(property: 'type', type: 'object', properties: [
            new OA\Property(property: 'value', type: 'string', enum: ['transactional', 'marketing'], example: 'transactional'),
            new OA\Property(property: 'label', type: 'string', example: 'transactional'),
        ]),
        new OA\Property(property: 'message', type: 'string', example: 'Your confirmation code: 1234'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class NotificationSchema {}

#[OA\Schema(
    schema: 'NotificationDetailResponse',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/NotificationResponse'),
        new OA\Schema(
            properties: [
                new OA\Property(property: 'recipients', type: 'object', properties: [
                    new OA\Property(property: 'total', type: 'integer', example: 100),
                    new OA\Property(property: 'queued', type: 'integer', example: 10),
                    new OA\Property(property: 'sent', type: 'integer', example: 20),
                    new OA\Property(property: 'delivered', type: 'integer', example: 65),
                    new OA\Property(property: 'discarded', type: 'integer', example: 5),
                ]),
            ]
        ),
    ]
)]
class NotificationDetailSchema {}

#[OA\Schema(
    schema: 'SendNotificationRequest',
    required: ['channel', 'type', 'message', 'subscriber_ids'],
    properties: [
        new OA\Property(property: 'channel', type: 'string', enum: ['sms', 'email'], example: 'sms'),
        new OA\Property(property: 'type', type: 'string', enum: ['transactional', 'marketing'], example: 'transactional'),
        new OA\Property(property: 'message', type: 'string', minLength: 1, maxLength: 1000, example: 'Your confirmation code: 1234'),
        new OA\Property(
            property: 'subscriber_ids',
            type: 'array',
            items: new OA\Items(type: 'string', example: 'user_42'),
            minItems: 1,
            maxItems: 1000,
        ),
    ]
)]
class SendNotificationRequestSchema {}

#[OA\Schema(
    schema: 'ValidationError',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The channel field is required.'),
        new OA\Property(property: 'errors', type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string', example: 'The selected channel is invalid.')
            )
        ),
    ]
)]
class ValidationErrorSchema {}
