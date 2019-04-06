<?php

declare(strict_types=1);

namespace Antidot\Infrastructure\Aura\Router;

use Antidot\Application\Http\Middleware\CallableMiddleware;
use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use Antidot\Application\Http\Middleware\PipedRouteMiddleware;
use Antidot\Application\Http\Route;
use Antidot\Application\Http\Router;
use Antidot\Container\MiddlewareFactory;
use Antidot\Container\RequestHandlerFactory;
use Aura\Router\Route as BaseRoute;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SplQueue;

use function array_pop;

class AuraRouter implements Router
{
    /** @var RouterContainer */
    private $routeContainer;
    /** @var MiddlewareFactory */
    private $middlewareFactory;
    /** @var RequestHandlerFactory */
    private $requestHandlerFactory;

    public function __construct(
        RouterContainer $routerContainer,
        MiddlewareFactory $middlewareFactory,
        RequestHandlerFactory $requestHandlerFactory
    ) {
        $this->routeContainer = $routerContainer;
        $this->middlewareFactory = $middlewareFactory;
        $this->requestHandlerFactory = $requestHandlerFactory;
    }

    public function append(Route $route): void
    {
        $baseRoute = new BaseRoute();
        $baseRoute->name($route->name());
        $baseRoute->path($route->path());
        $baseRoute->handler($route->pipeline());
        $baseRoute->allows($route->method());

        $this->routeContainer->getMap()->addRoute($baseRoute);
    }

    public function match(ServerRequestInterface $request): PipedRouteMiddleware
    {
        $route = $this->routeContainer->getMatcher()->match($request);
        if (false === $route) {
            return new PipedRouteMiddleware(new MiddlewarePipeline(new SplQueue()), true);
        }
        $pipeline = $this->getPipeline($route);

        return new PipedRouteMiddleware($pipeline, false);
    }

    private function getPipeline(BaseRoute $route): MiddlewarePipeline
    {
        $pipeline = new MiddlewarePipeline(new SplQueue());
        $middlewarePipeline = $route->handler;
        $handler = array_pop($middlewarePipeline);
        foreach ($middlewarePipeline as $middleware) {
            $pipeline->pipe($this->middlewareFactory->create($middleware));
        }

        $requestHandlerFactory = $this->requestHandlerFactory;
        $pipeline->pipe(new CallableMiddleware(
            static function (
                ServerRequestInterface $request
            ) use (
                $handler,
                $requestHandlerFactory
            ): ResponseInterface {
                $handler = $requestHandlerFactory->create($handler);

                return $handler->handle($request);
            }
        ));

        return $pipeline;
    }
}
