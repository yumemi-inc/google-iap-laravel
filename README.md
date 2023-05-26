# Laravel Authentication for Google IAP

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
   
   class AppGoogleUserResolver implements GoogleUserResolver
   {
       public function provide(GoogleUser $googleUser, UserProvider $userProvider): ?Authenticatable
       {
           return $userProvider->retrieveByCredentials([
               'google_user_id' => $googleUser->sub(),
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

4. Set the Google IAP guard as the default.
   ```php
   <?php // config/auth.php

   'defaults' => [
       'guard' => 'google',
   ]
   ```
