<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use Aura\Router\RouterContainer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouteDispatcherMiddleware implements MiddlewareInterface
{
    private $router;

    public function __construct(RouterContainer $routerContainer)
    {
        $this->router = $routerContainer;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->getMatcher()->match($request);
        if (false === $route) {
            return $handler->handle($request);
        }

        return $route->handler->handle($request);
    }
}
