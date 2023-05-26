<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel\Providers;

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use YumemiInc\GoogleIapLaravel\DefaultGoogleUserResolver;
use YumemiInc\GoogleIapLaravel\GoogleUserResolver;
use YumemiInc\GoogleIapLaravel\Http\GoogleIapGuard;
use YumemiInc\GoogleIapLaravel\Internal\Assert;

class GoogleIapServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->app->bind(GoogleUserResolver::class, DefaultGoogleUserResolver::class);

        $this->app->resolved(AuthManager::class)
            ? static::extendComponents($this->app->make(AuthManager::class)) // @codeCoverageIgnore
            : $this->app->afterResolving(AuthManager::class, $this->extendComponents(...));
    }

    protected function extendComponents(AuthManager $auth): void
    {
        $auth->extend(
            'google-iap',
            fn (Container $app, string $name, array $config) => (new GoogleIapGuard(
                $app->make('request'),
                $auth->createUserProvider($config['provider'] ?? null),
                $app->make(GoogleUserResolver::class),
                $config['services']['google-iap'],
            ))
                ->setProvider(Assert::nonNull($auth->createUserProvider($config['provider'] ?? null))),
        );
    }
}
