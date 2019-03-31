<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Router\RouterInterface;

final class RouteDispatcherMiddleware implements MiddlewareInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->match($request);
        if (false === $route) {
            return $handler->handle($request);
        }

        return $route->process($request, $handler);
    }
}
