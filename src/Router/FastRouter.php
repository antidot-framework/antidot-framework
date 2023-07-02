<?php

declare(strict_types=1);

namespace Antidot\Framework\Router;

use Antidot\Framework\Middleware\CallableMiddleware;
use Antidot\Framework\Middleware\MethodNotAllowedMiddleware;
use Antidot\Framework\Middleware\MiddlewareFactory;
use Antidot\Framework\Middleware\MiddlewarePipeline;
use Antidot\Framework\Middleware\PipedRouteMiddleware;
use Antidot\Framework\Middleware\RequestHandlerFactory;
use Antidot\Framework\Middleware\RouteNotFoundMiddleware;
use Closure;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;
use function array_pop;

final class FastRouter implements Router
{
    private readonly RouteCollector $routeCollector;

    /** @param array<string, array<MiddlewareInterface>> $routeCache */
    public function __construct(
        private readonly MiddlewareFactory $middlewareFactory,
        private readonly RequestHandlerFactory $requestHandlerFactory,
        private array $routeCache = []
    ) {
        $this->routeCollector = new RouteCollector(new Std(), new GroupCountBased());
    }

    public function append(Route $route): void
    {
        $this->routeCollector->addRoute($route->method, $route->path, $route->pipeline);
    }

    public function match(ServerRequestInterface $request): MiddlewareInterface
    {
        $dispatcher = new Dispatcher\GroupCountBased($this->routeCollector->getData());
        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return new RouteNotFoundMiddleware();
            case Dispatcher::FOUND:
                $routeId = sprintf('%s_%s', $request->getMethod(), $request->getUri()->getPath());
                /** @var array<array-key, Closure|MiddlewareInterface|string>  $pipes */
                $pipes = $routeInfo[1];
                $pipeline = $this->getPipeline($routeId, $pipes);
                /** @var array<string, mixed> $attributes */
                $attributes = $routeInfo[2];
                return new PipedRouteMiddleware($pipeline, $attributes);
        }

        return new MethodNotAllowedMiddleware();
    }

    /**
     * @param array<MiddlewareInterface|(callable():ResponseInterface)|string|string> $pipes
     * @return MiddlewarePipeline
     */
    private function getPipeline(string $routeId, array $pipes): MiddlewarePipeline
    {
        /** @var SplQueue<MiddlewareInterface> $queue */
        $queue = new SplQueue();
        $pipeline = new MiddlewarePipeline($queue);

        if (array_key_exists($routeId, $this->routeCache)) {
            foreach ($this->routeCache[$routeId] as $middleware) {
                $pipeline->pipe($middleware);
            }
            return $pipeline;
        }

        $middlewarePipeline = $pipes;
        /** @var RequestHandlerInterface $handler */
        $handler = array_pop($middlewarePipeline);

        foreach ($middlewarePipeline as $middleware) {
            $middleware = $this->middlewareFactory->create($middleware);
            $this->routeCache[$routeId][] = $middleware;
            $pipeline->pipe($middleware);
        }

        $requestHandlerFactory = $this->requestHandlerFactory;
        $requestHandler = new CallableMiddleware(
            static function (ServerRequestInterface $request) use (
                $handler,
                $requestHandlerFactory
            ): ResponseInterface {
                $handler = $requestHandlerFactory->create($handler);

                return $handler->handle($request);
            }
        );
        $this->routeCache[$routeId][] = $requestHandler;
        $pipeline->pipe($requestHandler);

        return $pipeline;
    }
}
