<?php

declare(strict_types=1);

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class SubscriberPaths
{
    #[OA\Get(
        path: '/api/v1/subscribers/{subscriberId}/notifications',
        summary: 'Get subscriber notification history',
        description: 'Returns paginated delivery history for a specific subscriber, optionally filtered by status.',
        tags: ['Subscribers'],
        parameters: [
            new OA\Parameter(name: 'subscriberId', in: 'path', required: true, schema: new OA\Schema(type: 'string', example: 'user_42')),
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['queued', 'sent', 'delivered', 'discarded'])),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20, maximum: 100)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of recipient records',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/RecipientResponse')
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function index(): void {}

    #[OA\Get(
        path: '/api/v1/subscribers/{subscriberId}/notifications/{notification}',
        summary: 'Get single notification status for subscriber',
        tags: ['Subscribers'],
        parameters: [
            new OA\Parameter(name: 'subscriberId', in: 'path', required: true, schema: new OA\Schema(type: 'string', example: 'user_42')),
            new OA\Parameter(name: 'notification', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Recipient delivery status with notification details',
                content: new OA\JsonContent(ref: '#/components/schemas/RecipientResponse')
            ),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(): void {}
}
