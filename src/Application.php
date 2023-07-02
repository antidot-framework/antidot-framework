<?php

declare(strict_types=1);

namespace Antidot\Framework;

use Antidot\Framework\Middleware\MiddlewareFactory;
use Antidot\Framework\Middleware\MiddlewarePipeline;
use Antidot\Framework\Router\RouteFactory;
use Antidot\Framework\Router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

/**
 * @psalm-type ArrayMiddleware array<MiddlewareInterface|RequestHandlerInterface|string|(callable():ResponseInterface)>
 */
final class Application implements RequestHandlerInterface
{
    public const NAME = 'antidot-react-http';

    /**
     * @param array<string> $middlewares
     * @param array<MiddlewareInterface> $middlewareCache
     */
    public function __construct(
        private readonly MiddlewareFactory $middlewareFactory,
        private readonly RouteFactory $routeFactory,
        private Router $router,
        private array $middlewares = [],
        private array $middlewareCache = [],
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var SplQueue<MiddlewareInterface> $queue */
        $queue = new SplQueue();
        $pipeline = new MiddlewarePipeline($queue);
        if ([] !== $this->middlewareCache) {
            foreach ($this->middlewareCache as $middleware) {
                $pipeline->pipe($middleware);
            }
            return $pipeline->handle($request);
        }

        foreach ($this->middlewares as $middleware) {
            $middleware = $this->middlewareFactory->create($middleware);
            $this->middlewareCache[] = $middleware;
            $pipeline->pipe($middleware);
        }

        return $pipeline->handle($request);
    }

    public function pipe(string $middlewareName): void
    {
        $this->middlewares[] = $middlewareName;
    }

    /**
     * @param RequestHandlerInterface|ArrayMiddleware|string|(callable():ResponseInterface) $middleware
     */
    public function get(string $uri, RequestHandlerInterface|array|string|callable $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['GET'], $name);
    }

    /**
     * @param RequestHandlerInterface|ArrayMiddleware|string|(callable():ResponseInterface) $middleware
     */
    public function post(string $uri, RequestHandlerInterface|array|string|callable $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['POST'], $name);
    }

    /**
     * @param RequestHandlerInterface|ArrayMiddleware|string|(callable():ResponseInterface) $middleware
     */
    public function patch(string $uri, RequestHandlerInterface|array|string|callable $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['PATCH'], $name);
    }

    /**
     * @param RequestHandlerInterface|ArrayMiddleware|string|(callable():ResponseInterface) $middleware
     */
    public function put(string $uri, RequestHandlerInterface|array|string|callable $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['PUT'], $name);
    }

    /**
     * @param RequestHandlerInterface|ArrayMiddleware|string|(callable():ResponseInterface) $middleware
     */
    public function delete(string $uri, RequestHandlerInterface|array|string|callable $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['DELETE'], $name);
    }

    /**
     * @param RequestHandlerInterface|ArrayMiddleware|string|(callable():ResponseInterface) $middleware
     */
    public function options(string $uri, RequestHandlerInterface|array|string|callable $middleware, string $name): void
    {
        $this->route($uri, $middleware, ['OPTIONS'], $name);
    }

    /**
     * @param RequestHandlerInterface|ArrayMiddleware|string|(callable():ResponseInterface) $middleware
     * @param array<string> $methods
     */
    public function route(
        string $uri,
        RequestHandlerInterface|array|string|callable $middleware,
        array $methods,
        string $name
    ): void {
        $middleware = is_array($middleware) ? $middleware : [$middleware];

        $this->router->append(
            $this->routeFactory->create($methods, $middleware, $uri, $name)
        );
    }
}
