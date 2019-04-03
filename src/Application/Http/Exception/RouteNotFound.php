<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Exception;

use RuntimeException;

use function sprintf;

final class RouteNotFound extends RuntimeException
{
    public static function withPath(string $getRequestTarget): self
    {
        return new self(sprintf('Route with path %s not found.', $getRequestTarget));
    }
}
