<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::controller(NotificationController::class)->group(function () {
            Route::post('/',              'store');
            Route::get('/',               'index');
            Route::get('/{notification}', 'show');
        });

        Route::prefix('reports')->controller(NotificationReportController::class)->group(function () {
            Route::post('/',                 'store');
            Route::get('/{report}',          'show');
            Route::get('/{report}/download', 'download');
        });
    });
});
