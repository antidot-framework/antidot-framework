<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Container\ContainerInterface;

class RequestHandlerFactoryFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerFactory
    {
        return new RequestHandlerFactory($container);
    }
}
