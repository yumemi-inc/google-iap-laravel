<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel\Http;

use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use YumemiInc\GoogleIapLaravel\Claims;
use YumemiInc\GoogleIapLaravel\DefaultGoogleUserResolver;
use YumemiInc\GoogleIapLaravel\GoogleIdTokenVerifier;
use YumemiInc\GoogleIapLaravel\GoogleUserResolver;
use YumemiInc\GoogleIapLaravel\MalformedClaimsException;

class GoogleIapGuard extends RequestGuard
{
    public function __construct(
        Request $request,
        private readonly GoogleIdTokenVerifier $googleIdTokenVerifier = new GoogleIdTokenVerifier(),
        private readonly GoogleUserResolver $userProviderAdapter = new DefaultGoogleUserResolver(),
    ) {
        parent::__construct(static::callback(...), $request);
    }

    /**
     * @throws MalformedClaimsException
     */
    public function callback(): ?Authenticatable
    {
        if (!\is_string($jwt = $this->request->header('x-goog-iap-jwt-assertion'))) {
            // Required HTTP header is not provided.
            return null;
        }

        if (!($claims = $this->googleIdTokenVerifier->verify($jwt)) instanceof Claims) {
            return null;
        }

        return $this->userProviderAdapter->provide(
            $claims,
            $this->provider,
        );
    }
}
