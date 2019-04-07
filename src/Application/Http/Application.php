<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Container\MiddlewareFactory;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

final class Application
{
    private $pipeline;
    private $router;
    private $runner;
    private $middlewareFactory;
    private $routeFactory;

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
        $this->route('GET', $uri, $middleware, $name);
    }

    public function post(string $uri, array $middleware, string $name): void
    {
        $this->route('POST', $uri, $middleware, $name);
    }

    public function patch(string $uri, array $middleware, string $name): void
    {
        $this->route('PATCH', $uri, $middleware, $name);
    }

    public function put(string $uri, array $middleware, string $name): void
    {
        $this->route('PUT', $uri, $middleware, $name);
    }

    public function delete(string $uri, array $middleware, string $name): void
    {
        $this->route('DELETE', $uri, $middleware, $name);
    }

    public function options(string $uri, array $middleware, string $name): void
    {
        $this->route('OPTIONS', $uri, $middleware, $name);
    }

    public function route(string $method, string $uri, array $middleware, string $name): void
    {
        $this->router->append(
            $this->routeFactory->create([$method], $middleware, $uri, $name)
        );
    }
}
