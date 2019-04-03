<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Container\MiddlewareFactory;
use Antidot\Infrastructure\Aura\Router\AuraRoute;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

final class Application
{
    private $pipeline;
    private $router;
    private $runner;
    private $middlewareFactory;

    public function __construct(
        RequestHandlerRunner $runner,
        Pipeline $pipeline,
        Router $router,
        MiddlewareFactory $middlewareFactory
    ) {
        $this->runner = $runner;
        $this->pipeline = $pipeline;
        $this->router = $router;
        $this->middlewareFactory = $middlewareFactory;
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
        $this->route('GET', $uri, $name, $middleware);
    }

    public function post(string $uri, array $middleware, string $name): void
    {
        $this->route('POST', $uri, $name, $middleware);
    }

    public function patch(string $uri, array $middleware, string $name): void
    {
        $this->route('PATCH', $uri, $name, $middleware);
    }

    public function put(string $uri, array $middleware, string $name): void
    {
        $this->route('PUT', $uri, $name, $middleware);
    }

    public function delete(string $uri, array $middleware, string $name): void
    {
        $this->route('DELETE', $uri, $name, $middleware);
    }

    public function options(string $uri, array $middleware, string $name): void
    {
        $this->route('OPTIONS', $uri, $name, $middleware);
    }

    private function route(string $method, string $uri, string $name, array $middleware): void
    {
        $this->router->append(
            new AuraRoute([$method], $name, $uri, $middleware)
        );
    }
}
