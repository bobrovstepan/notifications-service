<?php

namespace App\Providers;

use App\Channels\Factories\NotificationChannelFactory;
use App\Channels\Handlers\EmailChannelHandler;
use App\Channels\Handlers\TelegramChannelHandler;
use App\Enums\ChannelName;
use App\Events\NotificationCreated;
use App\Events\ReportRequested;
use App\Listeners\GenerateReportListener;
use App\Listeners\SendNotificationListener;
use App\Repositories\ChannelRepository;
use App\Repositories\Contracts\ChannelRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Reports\Contracts\ReportGeneratorInterface;
use App\Reports\NotificationReportGenerator;
use App\Repositories\Contracts\ReportRepositoryInterface;
use App\Repositories\NotificationRepository;
use App\Repositories\ReportRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(ChannelRepositoryInterface::class, ChannelRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
        $this->app->bind(ReportGeneratorInterface::class, NotificationReportGenerator::class);

        $this->app->singleton(NotificationChannelFactory::class, function ($app) {
            return new NotificationChannelFactory([
                ChannelName::Email->value    => $app->make(EmailChannelHandler::class),
                ChannelName::Telegram->value => $app->make(TelegramChannelHandler::class),
            ]);
        });
    }

    public function boot(): void
    {
        Event::listen(NotificationCreated::class, SendNotificationListener::class);
        Event::listen(ReportRequested::class, GenerateReportListener::class);
    }
}
