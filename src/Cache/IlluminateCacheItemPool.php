<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel\Cache;

use Illuminate\Contracts\Cache\Repository;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;
use Psr\SimpleCache\InvalidArgumentException;

class IlluminateCacheItemPool implements CacheItemPoolInterface
{
    /**
     * @var list<CacheItemInterface>
     */
    private array $deferred = [];

    public function __construct(
        private readonly Repository $repository,
        private readonly ClockInterface $clock,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getItem(string $key): CacheItemInterface
    {
        return new IlluminateCacheItem($this->repository, $this->clock, $key, $this->repository->get($key));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getItems(array $keys = []): iterable
    {
        foreach ($this->repository->getMultiple($keys) as $key => $value) {
            yield new IlluminateCacheItem($this->repository, $this->clock, $key, $value);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function hasItem(string $key): bool
    {
        return $this->repository->has($key);
    }

    public function clear(): bool
    {
        return $this->repository->clear();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function deleteItem(string $key): bool
    {
        return $this->repository->delete($key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function deleteItems(array $keys): bool
    {
        return $this->repository->deleteMultiple($keys);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function save(CacheItemInterface $item): bool
    {
        return $this->repository->set($item->getKey(), $item->get());
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[] = $item;

        return true;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function commit(): bool
    {
        foreach ($this->deferred as $item) {
            if (!$this->save($item)) {
                return false;
            }
        }

        return true;
    }
}
