<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel\Cache;

use Illuminate\Contracts\Cache\Repository;
use Psr\Cache\CacheItemInterface;
use Psr\Clock\ClockInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @template TCacheValue
 */
class IlluminateCacheItem implements CacheItemInterface
{
    /**
     * @param null|TCacheValue $value
     */
    public function __construct(
        private readonly Repository $repository,
        private readonly ClockInterface $clock,
        private readonly string $key,
        private mixed $value = null,
        private null|int|\DateInterval $ttl = null,
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return null|TCacheValue
     */
    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->value !== null;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function set(mixed $value): static
    {
        $this->repository->set($this->key, $value, $this->ttl);
        $this->value = $value;

        return $this;
    }

    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        $this->ttl = $expiration?->diff($this->clock->now());

        return $this;
    }

    public function expiresAfter(\DateInterval|int|null $time): static
    {
        $this->ttl = $time;

        return $this;
    }
}
