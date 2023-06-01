<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel\Http;

use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use YumemiInc\GoogleIapLaravel\Claims;
use YumemiInc\GoogleIapLaravel\DefaultGoogleUserResolver;
use YumemiInc\GoogleIapLaravel\GoogleIdTokenVerifier;
use YumemiInc\GoogleIapLaravel\GoogleUserResolver;
use YumemiInc\GoogleIapLaravel\Internal\Assert;
use YumemiInc\GoogleIapLaravel\Internal\AssertionException;
use YumemiInc\GoogleIapLaravel\MalformedClaimsException;

class GoogleIapGuard extends RequestGuard
{
    /**
     * @param array{allow_insecure_headers?: bool} $options
     */
    public function __construct(
        Request $request,
        private readonly GoogleIdTokenVerifier $googleIdTokenVerifier = new GoogleIdTokenVerifier(),
        private readonly GoogleUserResolver $userProviderAdapter = new DefaultGoogleUserResolver(),
        private readonly array $options = [],
    ) {
        parent::__construct(static::callback(...), $request);
    }

    /**
     * @throws MalformedClaimsException
     */
    public function callback(): ?Authenticatable
    {
        /** @var null|Claims $claims */
        $claims = null;

        if (\is_string($jwt = $this->request->header('x-goog-iap-jwt-assertion'))) {
            $claims = $this->googleIdTokenVerifier->verify($jwt);
        } elseif ($this->options['allow_insecure_headers'] ?? false) {
            try {
                $id = Assert::nonEmptyStringOrNull($this->request->header('x-goog-authenticated-user-id'));
                $email = Assert::nonEmptyStringOrNull($this->request->header('x-goog-authenticated-user-email'));
                $hd = ($email === null ? null : Assert::nonEmptyString(explode('@', $email)[1])) ?? 'example.com';

                if ($email !== null) {
                    $email = Claims::trimPrefix($email);
                }
            } catch (AssertionException $e) {
                throw new MalformedClaimsException($e);
            }

            $claims = new Claims([
                'exp' => \PHP_INT_MAX,
                'iat' => 1,
                'aud' => 'insecure',
                'iss' => 'https://cloud.google.com/iap',
                'hd' => $hd,
                'sub' => $id ?? 'accounts.google.com:0',
                'email' => $email ?? 'accounts.google.com:insecure@example.com',
            ]);
        }

        if (!$claims instanceof Claims) {
            return null;
        }

        return $this->userProviderAdapter->provide(
            $claims,
            $this->provider,
        );
    }
}
