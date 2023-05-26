<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel\Http;

use Google\Auth\AccessToken;
use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use YumemiInc\GoogleIapLaravel\DefaultGoogleUserResolver;
use YumemiInc\GoogleIapLaravel\GoogleUser;
use YumemiInc\GoogleIapLaravel\GoogleUserResolver;
use YumemiInc\GoogleIapLaravel\MalformedClaimsException;

class GoogleIapGuard extends RequestGuard
{
    public function __construct(
        Request $request,
        ?UserProvider $userProvider = null,
        private readonly GoogleUserResolver $userProviderAdapter = new DefaultGoogleUserResolver(),
        private readonly string $jwksUrl = AccessToken::IAP_CERT_URL,
    ) {
        parent::__construct(static::callback(...), $request, $userProvider);
    }

    /**
     * @throws MalformedClaimsException
     */
    public function callback(): ?Authenticatable
    {
        if (!\is_string($jwt = $this->request->header('x-goog-iap-jwt-assertion'))) {
            return null;
        }

        if (!($claims = (new AccessToken())->verify($jwt, [
            'certsLocation' => $this->jwksUrl,
        ]))) {
            return null;
        }

        return $this->userProviderAdapter->provide(
            new GoogleUser($claims),
            $this->provider,
        );
    }
}
