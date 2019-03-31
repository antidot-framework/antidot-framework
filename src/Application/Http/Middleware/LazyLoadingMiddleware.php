<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LazyLoadingMiddleware implements MiddlewareInterface
{
    private $container;
    private $middlewareName;

    public function __construct(
        ContainerInterface $container,
        string $middlewareName
    ) {
        $this->container = $container;
        $this->middlewareName = $middlewareName;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middleware = $this->container->get($this->middlewareName);

        return $middleware->process($request, $handler);
    }
}
