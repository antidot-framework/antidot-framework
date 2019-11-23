<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Middleware\CallableMiddleware;
use Antidot\Application\Http\Middleware\LazyLoadingMiddleware;
use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use Closure;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use SplQueue;

use function is_array;
use function is_string;
use function sprintf;

class MiddlewareFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $middlewareName
     * @return MiddlewareInterface
     */
    public function create($middlewareName): MiddlewareInterface
    {
        $middleware = null;

        if (is_string($middlewareName)) {
            return $this->lazyLoadMiddleware($middlewareName);
        }

        if (is_array($middlewareName)) {
            return $this->pipelineMiddleware($middlewareName);
        }

        if ($this->isClosure($middlewareName)) {
            return $this->callableMiddleware($middlewareName);
        }

        throw new InvalidArgumentException(sprintf('Invalid $middlewareName %s given.', $middlewareName));
    }

    private function callableMiddleware(Closure $middleware): MiddlewareInterface
    {
        return new CallableMiddleware($middleware);
    }

    private function lazyLoadMiddleware(string $middlewareName): MiddlewareInterface
    {
        return new LazyLoadingMiddleware($this->container, $middlewareName);
    }

    private function pipelineMiddleware(array $middlewareNames): MiddlewareInterface
    {
        $pipeline = new MiddlewarePipeline(new SplQueue());
        /** @var string $middlewareName */
        foreach ($middlewareNames as $middlewareName) {
            $pipeline->pipe($this->create($middlewareName));
        }

        return $pipeline;
    }

    /**
     * @param mixed $callable
     * @return bool
     */
    private function isClosure($callable): bool
    {
        return is_object($callable) && ($callable instanceof Closure);
    }
}
