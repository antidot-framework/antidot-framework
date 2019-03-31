<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Middleware\CallableMiddleware;
use Antidot\Application\Http\Middleware\LazyLoadingMiddleware;
use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class MiddlewareFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create($middlewareName): MiddlewareInterface
    {
        $middleware = null;

        if (\is_string($middlewareName)) {
            $middleware = $this->lazyLoadMiddleware($middlewareName);
        }

        if (\is_array($middlewareName)) {
            $middleware = $this->pipelineMiddleware($middlewareName);
        }

        if (\is_callable($middlewareName)) {
            $middleware = $this->callableMiddleware($middlewareName);
        }

        if (false === $middleware instanceof MiddlewareInterface) {
            throw new InvalidArgumentException(\sprintf('Invalid $middlewareName %s given.', $middlewareName));
        }

        return $middleware;
    }

    private function callableMiddleware(callable $middleware): MiddlewareInterface
    {
        return new CallableMiddleware($middleware);
    }

    private function lazyLoadMiddleware(string $middlewareName): MiddlewareInterface
    {
        return new LazyLoadingMiddleware($this->container, $middlewareName);
    }

    private function pipelineMiddleware(array $middlewareNames): MiddlewareInterface
    {
        $pipeline = new MiddlewarePipeline();
        /** @var string $middlewareName */
        foreach ($middlewareNames as $middlewareName) {
            $pipeline->pipe($this->create($middlewareName));
        }

        return $pipeline;
    }
}
