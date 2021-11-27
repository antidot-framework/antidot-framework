<?php

declare(strict_types=1);

namespace Antidot\Framework\Config;

use Antidot\Framework\Middleware\MiddlewareFactory;
use Antidot\Framework\Middleware\RequestHandlerFactory;
use Antidot\Framework\Router\FastRouter;
use Psr\Container\ContainerInterface;

final class FastRouterFactory
{
    public function __invoke(ContainerInterface $container): FastRouter
    {
        /** @var MiddlewareFactory $middlewareFactory */
        $middlewareFactory = $container->get(MiddlewareFactory::class);

        return new FastRouter(
            $middlewareFactory,
            new RequestHandlerFactory($container)
        );
    }
}
