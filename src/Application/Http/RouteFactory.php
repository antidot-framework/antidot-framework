<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

use Psr\Http\Server\MiddlewareInterface;

interface RouteFactory
{
    /**
     * @param array<string> $methods
     * @param array<MiddlewareInterface> $middleware
     */
    public function create(array $methods, array $middleware, string $uri, string $name): Route;
}
