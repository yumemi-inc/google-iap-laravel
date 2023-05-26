<?php

declare(strict_types=1);

namespace YumemiInc\GoogleIapLaravel;

class MalformedClaimsException extends \Exception
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct(
            'Malformed claims found.',
            previous: $previous,
        );
    }
}
