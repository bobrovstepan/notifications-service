<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::controller(NotificationController::class)->group(function () {
            Route::post('/', 'store')->name('notifications.store');
            Route::get('/', 'index')->name('notifications.index');
            Route::get('/{notification}', 'show')->name('notifications.show');
        });

        Route::prefix('reports')->controller(NotificationReportController::class)->group(function () {
            Route::post('/', 'store')->name('notifications.reports.store');
            Route::get('/{report}', 'show')->name('notifications.reports.show');
            Route::get('/{report}/download', 'download')->name('notifications.reports.download');
        });
    });
});
