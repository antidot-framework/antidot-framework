<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Application;
use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use Antidot\Application\Http\Response\ErrorResponseGenerator;
use Antidot\Application\Http\Router;
use Psr\Container\ContainerInterface;
use SplQueue;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;

final class ApplicationFactory
{
    public function __invoke(ContainerInterface $container): Application
    {
        return new Application(
            $container->get(EmitterStack::class),
            $container->get(ErrorResponseGenerator::class),
            $container->get(MiddlewareFactory::class),
            new MiddlewarePipeline(new SplQueue()),
            $container->get(Router::class)
        );
    }
}
