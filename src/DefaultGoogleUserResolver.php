<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class DefaultGoogleUserResolver implements GoogleUserResolver
{
    public function provide(Claims $googleUser, UserProvider $userProvider): ?Authenticatable
    {
        return $userProvider->retrieveById($googleUser->sub());
    }
}
