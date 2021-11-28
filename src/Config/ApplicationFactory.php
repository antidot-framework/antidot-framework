<?php

declare(strict_types=1);

namespace Antidot\Framework\Config;

use Antidot\Framework\Application;
use Antidot\Framework\Middleware\MiddlewareFactory;
use Antidot\Framework\Router\RouteFactory;
use Antidot\Framework\Router\Router;
use Psr\Container\ContainerInterface;

final class ApplicationFactory
{
    public function __invoke(ContainerInterface $container): Application
    {
        /** @var MiddlewareFactory $middlewareFactory */
        $middlewareFactory = $container->get(MiddlewareFactory::class);
        /** @var Router $router */
        $router = $container->get(Router::class);

        return new Application($middlewareFactory, new RouteFactory(), $router);
    }
}
