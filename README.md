# Laravel Authentication for Google IAP

> **Warning**  
> This is not an official product of YUMEMI Inc.

[![php](https://github.com/yumemi-inc/google-iap-laravel/actions/workflows/php.yml/badge.svg)](https://github.com/yumemi-inc/google-iap-laravel/actions/workflows/php.yml)

Authentication guard on Laravel for verifying requests from Google IAP (Identity-Aware Proxy).


## Prerequisites

- PHP 8.1 or later
- Laravel 9, 10, or 11 (dev)


## Getting Started

1. Require this package as a dependency.
   ```shell
   composer require yumemi-inc/google-iap-laravel
   ```

2. Implement `GoogleUserResolver` as you need.
   ```php
   <?php // app/Security/AppGoogleUserResolver.php (new)
   
   use Illuminate\Contracts\Auth\Authenticatable;
   use Illuminate\Contracts\Auth\UserProvider;
   use YumemiInc\GoogleIapLaravel\Claims;
   use YumemiInc\GoogleIapLaravel\GoogleUserResolver;
   
   class AppGoogleUserResolver implements GoogleUserResolver
   {
       public function provide(Claims $claims, UserProvider $userProvider): ?Authenticatable
       {
           return $userProvider->retrieveByCredentials([
               'google_user_id' => $claims->id(),
           ]);
       }
   }
   ```

3. Register the user resolver as a service.
   ```php
   <?php // app/Providers/AppServiceProvider.php
   
   use YumemiInc\GoogleIapLaravel\GoogleUserResolver;
   
   public function register(): void
   {
       $this->app->bind(GoogleUserProvider::class, AppGoogleUserProvider::class);
   }
   ```

4. Use the guard provided in this package.
   ```php
   <?php // config/auth.php

   'guards' => [
       'google-iap' => [
            'driver' => 'google-iap',
            'provider' => 'users',
        ],
   ]
   ```
