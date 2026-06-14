<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Repositories\SubscriberRepositoryInterface;
use App\Providers\Notification\EmailProvider;
use App\Providers\Notification\SmsProvider;
use App\Repositories\NotificationRepository;
use App\Repositories\SubscriberRepository;
use App\Services\ProviderFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            NotificationRepositoryInterface::class,
            NotificationRepository::class,
        );

        $this->app->singleton(
            SubscriberRepositoryInterface::class,
            SubscriberRepository::class,
        );

        $this->app->singleton(ProviderFactory::class, function ($app) {
            $factory = new ProviderFactory($app);

            $factory->register(SmsProvider::class);
            $factory->register(EmailProvider::class);

            return $factory;
        });
    }

    public function boot(): void {}
}
