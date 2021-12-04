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
    private RouteCollector $routeCollector;

    public function __construct(
        private MiddlewareFactory $middlewareFactory,
        private RequestHandlerFactory $requestHandlerFactory,
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
                /** @var array<array-key, Closure|MiddlewareInterface|string>  $pipes */
                $pipes = $routeInfo[1];
                $pipeline = $this->getPipeline($pipes);
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
    private function getPipeline(array $pipes): MiddlewarePipeline
    {
        /** @var SplQueue<MiddlewareInterface> $queue */
        $queue = new SplQueue();
        $pipeline = new MiddlewarePipeline($queue);
        $middlewarePipeline = $pipes;
        /** @var RequestHandlerInterface $handler */
        $handler = array_pop($middlewarePipeline);

        foreach ($middlewarePipeline as $middleware) {
            $pipeline->pipe($this->middlewareFactory->create($middleware));
        }

        $requestHandlerFactory = $this->requestHandlerFactory;
        $pipeline->pipe(new CallableMiddleware(
            static function (ServerRequestInterface $request) use (
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
