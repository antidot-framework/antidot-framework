<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Application;
use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Application\Http\Response\ErrorResponseGenerator;
use Antidot\Application\Http\RouteFactory;
use Antidot\Application\Http\Router;
use Psr\Container\ContainerInterface;
use SplQueue;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

final class ApplicationFactory
{
    public function __invoke(ContainerInterface $container): Application
    {
        $pipeline = new MiddlewarePipeline(new SplQueue());
        $runner = $this->getRunner($container, $pipeline);
        return new Application(
            $runner,
            $pipeline,
            $container->get(Router::class),
            $container->get(MiddlewareFactory::class),
            $container->get(RouteFactory::class)
        );
    }

    private function getRunner(ContainerInterface $container, Pipeline $pipeline): RequestHandlerRunner
    {
        return new RequestHandlerRunner(
            $pipeline,
            $container->get(EmitterStack::class),
            ($container->get(RequestFactory::class))(),
            $container->get(ErrorResponseGenerator::class)
        );
    }
}
