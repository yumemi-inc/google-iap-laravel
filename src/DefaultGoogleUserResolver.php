<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class DefaultGoogleUserResolver implements GoogleUserResolver
{
    public function provide(Claims $claims, UserProvider $userProvider): ?Authenticatable
    {
        return $userProvider->retrieveById($claims->sub());
    }
}
