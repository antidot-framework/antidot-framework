<?php

declare(strict_types=1);

namespace Antidot\Framework\Config;

use Antidot\Framework\Middleware\MiddlewareFactory;
use Psr\Container\ContainerInterface;

class MiddlewareFactoryFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareFactory
    {
        return new MiddlewareFactory($container);
    }
}
