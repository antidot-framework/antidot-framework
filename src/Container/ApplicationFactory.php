<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Application;
use Antidot\Application\Http\Middleware\SyncMiddlewareQueue;
use Antidot\Application\Http\WebServerApplication;
use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Application\Http\Response\ErrorResponseGenerator;
use Antidot\Application\Http\RouteFactory;
use Antidot\Application\Http\Router;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Psr\Container\ContainerInterface;

final class ApplicationFactory
{
    public function __invoke(ContainerInterface $container): Application
    {
        $pipeline = new MiddlewarePipeline(new SyncMiddlewareQueue());
        $runner = $this->getRunner($container, $pipeline);
        /** @var Router $router */
        $router = $container->get(Router::class);
        /** @var MiddlewareFactory $middleware */
        $middleware = $container->get(MiddlewareFactory::class);
        /** @var RouteFactory $routeFactory */
        $routeFactory = $container->get(RouteFactory::class);

        return new WebServerApplication($runner, $pipeline, $router, $middleware, $routeFactory);
    }

    private function getRunner(ContainerInterface $container, Pipeline $pipeline): RequestHandlerRunner
    {
        /** @var EmitterInterface $emitterStack */
        $emitterStack = $container->get(EmitterStack::class);
        /** @var RequestFactory $requestFactory */
        $requestFactory = $container->get(RequestFactory::class);
        /** @var ErrorResponseGenerator $errorResponseGenerator */
        $errorResponseGenerator = $container->get(ErrorResponseGenerator::class);

        return new RequestHandlerRunner($pipeline, $emitterStack, $requestFactory(), $errorResponseGenerator);
    }
}
