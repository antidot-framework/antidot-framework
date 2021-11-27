<?php

declare(strict_types=1);

namespace Antidot\Framework\Router;

/**
 * @psalm-import-type ArrayMiddleware from \Antidot\Framework\Application
 */
final class RouteFactory
{
    /**
     * @param array<string> $methods
     * @param ArrayMiddleware $middleware
     */
    public function create(array $methods, array $middleware, string $uri, string $name): Route
    {
        return new Route($methods, $name, $uri, $middleware);
    }
}
