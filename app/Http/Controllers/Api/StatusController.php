<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\SubscriberNotificationsDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriberNotificationsRequest;
use App\Http\Resources\NotificationRecipientResource;
use App\Models\Notification;
use App\Services\SubscriberService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StatusController extends Controller
{
    public function __construct(
        private readonly SubscriberService $subscriberService,
    ) {}

    public function index(
        SubscriberNotificationsRequest $request,
        string $subscriberId,
    ): JsonResponse {
        $dto = SubscriberNotificationsDTO::fromRequest($request, $subscriberId);
        $recipients = $this->subscriberService->getNotifications($dto);

        return response()->json(
            NotificationRecipientResource::collection($recipients)->resolve(),
            Response::HTTP_OK,
        );
    }

    public function show(
        string $subscriberId,
        Notification $notification,
    ): JsonResponse {
        $recipient = $this->subscriberService->getNotification($subscriberId, $notification->id);

        return response()->json(
            NotificationRecipientResource::make($recipient)->resolve(),
            Response::HTTP_OK,
        );
    }
}
