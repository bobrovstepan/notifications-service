<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\DTO\SendNotificationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly NotificationRepositoryInterface $notificationRepository,
    ) {}

    public function send(SendNotificationRequest $request): JsonResponse
    {
        $dto = SendNotificationDTO::fromRequest($request);

        if ($dto->idempotencyKey) {
            $cached = $this->notificationService->findCachedResponse($dto->idempotencyKey);

            if ($cached) {
                return $this->idempotentResponse($cached);
            }
        }

        $notification = $this->notificationService->dispatch($dto);
        $response = NotificationResource::make($notification)->resolve();

        if ($dto->idempotencyKey) {
            $this->notificationService->cacheResponse($dto->idempotencyKey, $notification, $response);
        }

        return response()->json($response, Response::HTTP_ACCEPTED);
    }

    public function show(Notification $notification): JsonResponse
    {
        $notification = $this->notificationRepository->findWithRelations($notification->id);

        return response()->json(
            NotificationResource::make($notification)->resolve(),
            Response::HTTP_OK,
        );
    }

    private function idempotentResponse(array $cached): JsonResponse
    {
        return response()
            ->json($cached, Response::HTTP_OK)
            ->header('X-Idempotent-Replayed', 'true');
    }
}
