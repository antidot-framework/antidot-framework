<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

interface RouteFactory
{
    public function create(array $methods, array $middleware, string $uri, string $name): Route;
}
