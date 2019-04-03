<?php

declare(strict_types=1);

namespace Antidot\Infrastructure\Aura\Router;

use Antidot\Application\Http\Middleware\CallableMiddleware;
use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use Antidot\Application\Http\Middleware\PipedRoute;
use Antidot\Application\Http\Route;
use Antidot\Application\Http\Router;
use Antidot\Container\MiddlewareFactory;
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
    private $factory;

    public function __construct(RouterContainer $routerContainer, MiddlewareFactory $factory)
    {
        $this->routeContainer = $routerContainer;
        $this->factory = $factory;
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

    public function match(ServerRequestInterface $request): PipedRoute
    {
        $route = $this->routeContainer->getMatcher()->match($request);
        if (false === $route) {
            return new PipedRoute(new MiddlewarePipeline(new SplQueue()), true);
        }
        $pipeline = $this->getPipeline($route);

        return new PipedRoute($pipeline, false);
    }

    private function getPipeline(BaseRoute $route): MiddlewarePipeline
    {
        $pipeline = new MiddlewarePipeline(new SplQueue());
        $middlewarePipeline = $route->handler;
        $handler = array_pop($middlewarePipeline);
        foreach ($middlewarePipeline as $middleware) {
            $pipeline->pipe($this->factory->create($middleware));
        }
        $pipeline->pipe(new CallableMiddleware(
            static function (ServerRequestInterface $request) use ($handler): ResponseInterface {
                return $handler($request);
            }
        ));

        return $pipeline;
    }
}
