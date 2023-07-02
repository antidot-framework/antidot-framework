<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use SplQueue;

use function is_array;
use function is_string;

final class MiddlewareFactory
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    /**
     * @param MiddlewareInterface|(callable():ResponseInterface)|array<MiddlewareInterface>|string $middlewareName
     */
    public function create(MiddlewareInterface|callable|array|string $middlewareName): MiddlewareInterface
    {
        if ($middlewareName instanceof MiddlewareInterface) {
            return $middlewareName;
        }

        if (is_string($middlewareName)) {
            return $this->lazyLoadMiddleware($middlewareName);
        }

        if (is_array($middlewareName)) {
            return $this->pipelineMiddleware($middlewareName);
        }

        return $this->callableMiddleware($middlewareName);
    }

    private function callableMiddleware(callable $middleware): MiddlewareInterface
    {
        return new CallableMiddleware(Closure::fromCallable($middleware));
    }

    private function lazyLoadMiddleware(string $middlewareName): MiddlewareInterface
    {
        return new LazyLoadingMiddleware($this->container, $middlewareName);
    }

    /**
     * @param array<mixed> $middlewareNames
     */
    private function pipelineMiddleware(array $middlewareNames): MiddlewareInterface
    {
        /** @var SplQueue<MiddlewareInterface> $queue */
        $queue = new SplQueue();
        $pipeline = new MiddlewarePipeline($queue);
        /** @var string $middlewareName */
        foreach ($middlewareNames as $middlewareName) {
            $pipeline->pipe($this->create($middlewareName));
        }

        return $pipeline;
    }
}
