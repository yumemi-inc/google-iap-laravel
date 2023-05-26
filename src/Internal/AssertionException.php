<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel\Internal;

class AssertionException extends \Exception
{
    public function __construct(string $expected, string $actual, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            "Assertion failed, expected {$expected} but got {$actual}.",
            $code,
            $previous,
        );
    }
}
