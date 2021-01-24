<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Container\MiddlewareFactory;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;

class WebServerApplication implements Application
{
    protected Pipeline $pipeline;
    protected Router $router;
    protected RequestHandlerRunner $runner;
    protected MiddlewareFactory $middlewareFactory;
    protected RouteFactory $routeFactory;

    public function __construct(
        RequestHandlerRunner $runner,
        Pipeline $pipeline,
        Router $router,
        MiddlewareFactory $middlewareFactory,
        RouteFactory $routeFactory
    ) {
        $this->runner = $runner;
        $this->pipeline = $pipeline;
        $this->router = $router;
        $this->middlewareFactory = $middlewareFactory;
        $this->routeFactory = $routeFactory;
    }

    public function run(): void
    {
        $this->runner->run();
    }

    public function pipe(string $middlewareName): void
    {
        $this->pipeline->pipe($this->middlewareFactory->create($middlewareName));
    }

    public function get(string $uri, array $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['GET'], $name);
    }

    public function post(string $uri, array $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['POST'], $name);
    }

    public function patch(string $uri, array $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['PATCH'], $name);
    }

    public function put(string $uri, array $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['PUT'], $name);
    }

    public function delete(string $uri, array $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['DELETE'], $name);
    }

    public function options(string $uri, array $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['OPTIONS'], $name);
    }

    public function route(string $uri, array $middleware, array $methods, string $name): void
    {
        $this->router->append(
            $this->routeFactory->create($methods, $middleware, $uri, $name)
        );
    }
}
