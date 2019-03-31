<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Container\MiddlewareFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouterInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

final class Application
{
    private $emitterStack;
    private $serverRequestErrorResponseGenerator;
    private $middlewareFactory;
    private $pipeline;
    private $router;

    public function __construct(
        EmitterStack $emitterStack,
        ServerRequestErrorResponseGenerator $serverRequestErrorResponseGenerator,
        MiddlewareFactory $middlewareFactory,
        Pipeline $pipeline,
        RouterInterface $router
    ) {
        $this->emitterStack = $emitterStack;
        $this->serverRequestErrorResponseGenerator = $serverRequestErrorResponseGenerator;
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
            $this->serverRequestErrorResponseGenerator
        );
        $runner->run();
    }

    public function pipe(string $middlewareName): void
    {
        $this->pipeline->pipe($this->middlewareFactory->create($middlewareName));
    }

    public function get(string $uri, array $middleware, string $name): void
    {
        $this->router->addRoute(
            new Route($uri, $this->middlewareFactory->create($middleware), ['GET'], $name)
        );
    }

    public function post(string $uri, array $middleware, string $name): void
    {
        $this->router->addRoute(
            new Route($uri, $this->middlewareFactory->create($middleware), ['POST'], $name)
        );
    }

    public function patch(string $uri, array $middleware, string $name): void
    {
        $this->router->addRoute(
            new Route($uri, $this->middlewareFactory->create($middleware), ['PATCH'], $name)
        );
    }

    public function put(string $uri, array $middleware, string $name): void
    {
        $this->router->addRoute(
            new Route($uri, $this->middlewareFactory->create($middleware), ['PUT'], $name)
        );
    }

    public function delete(string $uri, array $middleware, string $name): void
    {
        $this->router->addRoute(
            new Route($uri, $this->middlewareFactory->create($middleware), ['DELETE'], $name)
        );
    }

    public function options(string $uri, array $middleware, string $name): void
    {
        $this->router->addRoute(
            new Route($uri, $this->middlewareFactory->create($middleware), ['OPTIONS'], $name)
        );
    }
}
