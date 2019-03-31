<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Application;
use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use Antidot\Application\Http\ServerRequestErrorResponseGenerator;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;

final class ApplicationFactory
{
    public function __invoke(ContainerInterface $container): Application
    {
        return new Application(
            $container->get(EmitterStack::class),
            $container->get(ServerRequestErrorResponseGenerator::class),
            new MiddlewareFactory($container),
            new MiddlewarePipeline(),
            $container->get(RouterInterface::class)
        );
    }
}
