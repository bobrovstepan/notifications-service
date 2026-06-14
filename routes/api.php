<?php

declare(strict_types=1);

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\StatusController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/notifications', [NotificationController::class, 'send'])
        ->name('notifications.send');

    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])
        ->name('notifications.show');

    Route::get('/subscribers/{subscriberId}/notifications', [StatusController::class, 'index'])
        ->name('subscribers.notifications.index');

    Route::get('/subscribers/{subscriberId}/notifications/{notification}', [StatusController::class, 'show'])
        ->name('subscribers.notifications.show');

});
