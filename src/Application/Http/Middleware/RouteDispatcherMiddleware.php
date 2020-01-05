<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use Antidot\Application\Http\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouteDispatcherMiddleware implements MiddlewareInterface
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->match($request);

        if (true === $route->isFail()) {
            return $handler->handle($request);
        }

        return $route->process($request, $handler);
    }
}
