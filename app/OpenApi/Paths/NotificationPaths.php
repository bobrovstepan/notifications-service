<?php

declare(strict_types=1);

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class NotificationPaths
{
    #[OA\Post(
        path: '/api/v1/notifications',
        summary: 'Send bulk notification',
        description: 'Dispatches SMS or Email notifications to multiple subscribers. Jobs are placed in priority queues based on notification type.',
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(
                name: 'X-Idempotency-Key',
                in: 'header',
                required: false,
                description: 'Unique key to prevent duplicate sends on retry.',
                schema: new OA\Schema(type: 'string', example: 'order-123-notification')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/SendNotificationRequest')
        ),
        responses: [
            new OA\Response(
                response: 202,
                description: 'Notification accepted and queued',
                content: new OA\JsonContent(ref: '#/components/schemas/NotificationResponse')
            ),
            new OA\Response(
                response: 200,
                description: 'Idempotent replay — cached response returned',
                headers: [
                    new OA\Header(
                        header: 'X-Idempotent-Replayed',
                        schema: new OA\Schema(type: 'string', example: 'true')
                    ),
                ],
                content: new OA\JsonContent(ref: '#/components/schemas/NotificationResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function send(): void {}

    #[OA\Get(
        path: '/api/v1/notifications/{id}',
        summary: 'Get notification status',
        description: 'Returns notification details with a breakdown of recipient delivery statuses.',
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Notification UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '018f1e2a-1234-7abc-8def-000000000001')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Notification details with recipient breakdown',
                content: new OA\JsonContent(ref: '#/components/schemas/NotificationDetailResponse')
            ),
            new OA\Response(response: 404, description: 'Notification not found'),
        ]
    )]
    public function show(): void {}
}
