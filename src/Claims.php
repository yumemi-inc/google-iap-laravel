<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel;

use YumemiInc\GoogleIapLaravel\Internal\Assert;
use YumemiInc\GoogleIapLaravel\Internal\AssertionException;

class Claims
{
    /**
     * @param array{
     *     exp: positive-int,
     *     iat: positive-int,
     *     aud: non-empty-string,
     *     iss: non-empty-string,
     *     hd: non-empty-string,
     *     sub: non-empty-string,
     *     email: non-empty-string,
     * } $claims
     *
     * @see https://cloud.google.com/iap/docs/signed-headers-howto#verifying_the_jwt_payload
     */
    public function __construct(
        public readonly array $claims,
    ) {
    }

    /**
     * Must be in the future. The time is measured in seconds since the UNIX epoch. Allow 30 seconds for skew. The
     * maximum lifetime of a token is 10 minutes + 2 * skew.
     *
     * @return positive-int
     */
    public function exp(): int
    {
        return $this->claims['exp'];
    }

    /**
     * Must be in the past. The time is measured in seconds since the UNIX epoch. Allow 30 seconds for skew.
     *
     * @return positive-int
     */
    public function iat(): int
    {
        return $this->claims['iat'];
    }

    /**
     * Must be a string with the following values:
     *
     * - App Engine: /projects/PROJECT_NUMBER/apps/PROJECT_ID
     * - Compute Engine and GKE: /projects/PROJECT_NUMBER/global/backendServices/SERVICE_ID
     *
     * @return non-empty-string
     */
    public function aud(): string
    {
        return $this->claims['aud'];
    }

    /**
     * Must be https://cloud.google.com/iap.
     *
     * @return non-empty-string
     */
    public function iss(): string
    {
        return $this->claims['iss'];
    }

    /**
     * If an account belongs to a hosted domain, the hd claim is provided to differentiate the domain the account is
     * associated with.
     *
     * @return non-empty-string
     */
    public function hd(): string
    {
        return $this->claims['hd'];
    }

    /**
     * The unique, stable identifier for the user. Use this value instead of the x-goog-authenticated-user-id header.
     *
     * @return non-empty-string
     */
    public function sub(): string
    {
        return $this->claims['sub'];
    }

    /**
     * User email address.
     *
     * - Use this value instead of the x-goog-authenticated-user-email header.
     * - Unlike that header and the sub claim, this value doesn't have a namespace prefix.
     *
     * @return non-empty-string
     */
    public function email(): string
    {
        return $this->claims['email'];
    }

    /**
     * Get user identifier of the Google account. Since `sub` claim have a namespace prefix (usually `accounts.google.com`
     * other than the account ID, this function trims the prefix.
     *
     * @return non-empty-string
     *
     * @throws MalformedClaimsException
     */
    public function id(): string
    {
        return self::trimPrefix($this->sub());
    }

    /**
     * @param non-empty-string $prefixed
     *
     * @return non-empty-string
     *
     * @throws MalformedClaimsException
     *
     * @internal
     */
    public static function trimPrefix(string $prefixed): string
    {
        // If not prefix is detected in the value, remains the value untouched.
        if (str_contains($prefixed, ':')) {
            return $prefixed;
        }

        try {
            return Assert::nonEmptyString(Assert::in(1, explode(':', $prefixed)));
        } catch (AssertionException $e) {
            throw new MalformedClaimsException($e);
        }
    }

    /**
     * @param array<array-key, mixed> $claims
     *
     * @throws MalformedClaimsException
     */
    public static function from(
        array $claims,
    ): self {
        try {
            return new self([
                'exp' => Assert::positiveInt(Assert::in('exp', $claims)),
                'iat' => Assert::positiveInt(Assert::in('iat', $claims)),
                'aud' => Assert::nonEmptyString(Assert::in('aud', $claims)),
                'iss' => Assert::nonEmptyString(Assert::in('iss', $claims)),
                'hd' => Assert::nonEmptyString(Assert::in('hd', $claims)),
                'sub' => Assert::nonEmptyString(Assert::in('sub', $claims)),
                'email' => Assert::nonEmptyString(Assert::in('email', $claims)),
            ]);
        } catch (AssertionException $e) {
            throw new MalformedClaimsException($e);
        }
    }
}
