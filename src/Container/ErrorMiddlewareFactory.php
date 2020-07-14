<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Middleware\ErrorMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class ErrorMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        return new ErrorMiddleware($container->get('config')['debug']);
    }
}
