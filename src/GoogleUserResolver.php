<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

interface GoogleUserResolver
{
    public function provide(GoogleUser $googleUser, UserProvider $userProvider): ?Authenticatable;
}
