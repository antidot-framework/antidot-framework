<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyLoadingMiddleware implements MiddlewareInterface
{
    private ContainerInterface $container;
    private string $middlewareName;

    public function __construct(ContainerInterface $container, string $middlewareName)
    {
        $this->assertThatContainerHasMiddleware($container, $middlewareName);
        $this->container = $container;
        $this->middlewareName = $middlewareName;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middleware = $this->container->get($this->middlewareName);

        return $middleware->process($request, $handler);
    }

    private function assertThatContainerHasMiddleware(ContainerInterface $container, string $middlewareName): void
    {
        if (false === $container->has($middlewareName)) {
            throw new InvalidArgumentException('Invalid middleware name given.');
        }
    }
}
