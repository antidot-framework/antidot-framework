<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Application\Http\Response\ErrorResponseGenerator;
use Antidot\Container\MiddlewareFactory;
use Antidot\Infrastructure\Aura\Router\AuraRoute;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

final class Application
{
    private $emitterStack;
    private $errorResponseGenerator;
    private $middlewareFactory;
    private $pipeline;
    private $router;

    public function __construct(
        EmitterStack $emitterStack,
        ErrorResponseGenerator $errorResponseGenerator,
        MiddlewareFactory $middlewareFactory,
        Pipeline $pipeline,
        Router $router
    ) {
        $this->emitterStack = $emitterStack;
        $this->errorResponseGenerator = $errorResponseGenerator;
        $this->middlewareFactory = $middlewareFactory;
        $this->pipeline = $pipeline;
        $this->router = $router;
    }

    public function run(): void
    {
        $runner = new RequestHandlerRunner(
            $this->pipeline,
            $this->emitterStack,
            static function (): RequestInterface {
                return ServerRequestFactory::fromGlobals(
                    $_SERVER,
                    $_GET,
                    $_POST,
                    $_COOKIE,
                    $_FILES
                );
            },
            $this->errorResponseGenerator
        );
        $runner->run();
    }

    public function pipe(string $middlewareName): void
    {
        $this->pipeline->pipe($this->middlewareFactory->create($middlewareName));
    }

    public function get(string $uri, array $middleware, string $name): void
    {
        $this->route('GET', $uri, $name, $middleware);
    }

    private function route(string $method, string $uri, string $name, array $middleware): void
    {
        $this->router->append(
            new AuraRoute([$method], $name, $uri, $middleware)
        );
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
}
