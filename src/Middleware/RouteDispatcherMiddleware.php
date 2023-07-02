<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Antidot\Framework\Router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouteDispatcherMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Router $router
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->match($request);

        return $route->process($request, $handler);
    }
}
