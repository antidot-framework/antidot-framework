<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyLoadingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ContainerInterface $container,
        private string $middlewareName
    ) {
        $this->assertThatContainerHasMiddleware($container, $middlewareName);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var MiddlewareInterface $middleware */
        $middleware = $this->container->get($this->middlewareName);

        return $middleware->process($request, $handler);
    }

    private function assertThatContainerHasMiddleware(ContainerInterface $container, string $middlewareName): void
    {
        if (false === $container->has($middlewareName)) {
            throw new InvalidArgumentException(sprintf('Invalid middleware name given %s.', $middlewareName));
        }
    }
}
