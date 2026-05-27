<?php

namespace App\Http\Controllers;

use App\DTO\Notification\CreateNotificationDTO;
use App\DTO\Notification\NotificationFilterDTO;
use App\Http\Requests\Notification\IndexNotificationRequest;
use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $service) {}

    public function store(StoreNotificationRequest $request): JsonResponse
    {
        $notification = $this->service->create(CreateNotificationDTO::fromRequest($request));

        return (new NotificationResource($notification->load('channel')))
            ->additional(['message' => __('notification.created')])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Notification $notification): JsonResponse
    {
        return (new NotificationResource($notification->load('channel')))
            ->additional(['message' => __('notification.retrieved')])
            ->response();
    }

    public function index(IndexNotificationRequest $request): JsonResponse
    {
        $notifications = $this->service->getUserHistory(NotificationFilterDTO::fromRequest($request));

        return NotificationResource::collection($notifications)
            ->additional(['message' => __('notification.listed')])
            ->response();
    }
}
