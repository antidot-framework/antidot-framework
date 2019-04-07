<?php

declare(strict_types=1);

namespace Antidot\Infrastructure\Aura\Router;

use Antidot\Application\Http\Route;
use Antidot\Application\Http\RouteFactory;

class AuraRouteFactory implements RouteFactory
{
    public function create(array $methods, array $middleware, string $uri, string $name): Route
    {
        return new AuraRoute($methods, $name, $uri, $middleware);
    }
}
