<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Middleware\ErrorMiddleware;
use Psr\Container\ContainerInterface;

class ErrorMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ErrorMiddleware($container->get('config')['debug']);
    }
}
