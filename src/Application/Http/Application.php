<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

use Antidot\Application\Http\Handler\CallableRequestHandler;
use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Container\MiddlewareFactory;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\ServerRequestFactory;
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
        RouterContainer $router
    ) {
        $this->emitterStack = $emitterStack;
        $this->serverRequestErrorResponseGenerator = $serverRequestErrorResponseGenerator;
        $this->middlewareFactory = $middlewareFactory;
        $this->pipeline = $pipeline;
        $this->router = $router;
    }

    public function run(): void
    {
        $request = $this->getRequest();

        $runner = new RequestHandlerRunner(
            $this->pipeline,
            $this->emitterStack,
            static function () use ($request) {
                return $request;
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
        $map = $this->router->getMap();
        $handler = $this->getHandler($middleware);

        $map->get($name, $uri, $handler);
    }

    public function post(string $uri, array $middleware, string $name): void
    {
        $map = $this->router->getMap();
        $handler = $this->getHandler($middleware);

        $map->post($name, $uri, $handler);
    }

    public function patch(string $uri, array $middleware, string $name): void
    {
        $map = $this->router->getMap();
        $handler = $this->getHandler($middleware);

        $map->patch($name, $uri, $handler);
    }

    public function put(string $uri, array $middleware, string $name): void
    {
        $map = $this->router->getMap();
        $handler = $this->getHandler($middleware);

        $map->put($name, $uri, $handler);
    }

    public function delete(string $uri, array $middleware, string $name): void
    {
        $map = $this->router->getMap();
        $handler = $this->getHandler($middleware);

        $map->delete($name, $uri, $handler);
    }

    public function options(string $uri, array $middleware, string $name): void
    {
        $map = $this->router->getMap();
        $handler = $this->getHandler($middleware);

        $map->options($name, $uri, $handler);
    }

    private function getRequest(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
    }

    private function getHandler(array $pipe): RequestHandlerInterface
    {
        $handler = \array_pop($pipe);
        foreach ($pipe as $middleware) {
            $this->pipeline->pipe($middleware);
        }

        return \is_callable($handler) ? new CallableRequestHandler($handler) : $handler;
    }
}
