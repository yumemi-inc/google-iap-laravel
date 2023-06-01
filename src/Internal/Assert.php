<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel\Internal;

class Assert
{
    /**
     * @phpstan-return ($value is int ? int : never)
     *
     * @throws AssertionException
     */
    public static function int(mixed $value): int
    {
        if (!\is_int($value)) {
            throw new AssertionException('int', \gettype($value));
        }

        return $value;
    }

    /**
     * @phpstan-return ($value is positive-int ? positive-int : never)
     *
     * @throws AssertionException
     */
    public static function positiveInt(mixed $value): int
    {
        if (($i = self::int($value)) <= 0) {
            throw new AssertionException('positive-int', 'negative-int or 0');
        }

        return $i;
    }

    /**
     * @phpstan-return ($value is string ? string : never)
     *
     * @throws AssertionException
     */
    public static function string(mixed $value): string
    {
        if (!\is_string($value)) {
            throw new AssertionException('string', \gettype($value));
        }

        return $value;
    }

    /**
     * @phpstan-return ($value is non-empty-string ? non-empty-string : never)
     *
     * @throws AssertionException
     */
    public static function nonEmptyString(mixed $value): string
    {
        if (($s = self::string($value)) === '') {
            throw new AssertionException('non-empty-string', 'empty string');
        }

        return $s;
    }

    /**
     * @phpstan-return ($value is non-empty-string ? non-empty-string : ($value is null ? null : never))
     *
     * @throws AssertionException
     */
    public static function nonEmptyStringOrNull(mixed $value): null|string
    {
        try {
            return self::nonEmptyString($value);
        } catch (AssertionException) {
            return self::null($value);
        }
    }

    /**
     * @template T
     *
     * @param null|T $value
     *
     * @return T
     *
     * @throws AssertionException
     */
    public static function nonNull(mixed $value): mixed
    {
        if ($value === null) {
            throw new AssertionException('non-null value', 'null');
        }

        return $value;
    }

    /**
     * @phpstan-return ($value is null ? null : never)
     *
     * @throws AssertionException
     */
    public static function null(mixed $value): mixed
    {
        if ($value !== null) {
            throw new AssertionException('null', 'non-null value');
        }

        return null;
    }

    /**
     * @template TKey of array-key
     * @template TValue
     *
     * @phpstan-param TKey        $needle
     *
     * @param array<TKey, TValue> $haystack
     *
     * @return never|TValue
     *
     * @throws AssertionException
     */
    public static function in(int|string $needle, array $haystack): mixed
    {
        if (!\array_key_exists($needle, $haystack)) {
            throw new AssertionException("array contains key {$needle}", 'array without the key');
        }

        return $haystack[$needle];
    }
}
