<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NotificationProviderInterface;
use App\Enums\NotificationChannel;
use App\Exceptions\ProviderNotFoundException;
use Illuminate\Contracts\Container\Container;

class ProviderFactory
{
    /** @var NotificationProviderInterface[] */
    private array $providers = [];

    public function __construct(
        private readonly Container $container,
    ) {}

    public function register(string $providerClass): void
    {
        $this->providers[] = $this->container->make($providerClass);
    }

    public function make(NotificationChannel $channel): NotificationProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($channel->value)) {
                return $provider;
            }
        }

        throw new ProviderNotFoundException($channel);
    }
}
