<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel;

use Google\Auth\AccessToken;

class GoogleIdTokenVerifier
{
    /**
     * @param null|string $issuer   If provided, verifies the issuer of the token. Pass null to disable.
     * @param null|string $audience If provided, verifies the audience of the token. Pass null to disable.
     */
    public function __construct(
        private readonly string $jwksUrl = AccessToken::IAP_CERT_URL,
        private readonly ?string $issuer = 'https://cloud.google.com/iap',
        private readonly ?string $audience = null,
    ) {
    }

    /**
     * Verifies the JWT issued by Google IAP.
     *
     * @return null|Claims claims in the JWT, or null if the token is invalid or malformed
     *
     * @throws MalformedClaimsException
     */
    public function verify(string $jwt): ?Claims
    {
        if (!($claims = (new AccessToken())->verify($jwt, [
            'certsLocation' => $this->jwksUrl,
        ]))) {
            // Invalid or malformed token.
            return null;
        }

        $claims = new Claims($claims);

        if ($this->issuer !== null && $claims->iss() !== $this->issuer) {
            // Issuer verification failed.
            return null;
        }

        if ($this->audience !== null && $claims->aud() !== $this->audience) {
            // Audience verification failed.
            return null;
        }

        return $claims;
    }
}
