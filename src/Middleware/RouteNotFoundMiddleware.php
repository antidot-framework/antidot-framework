<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use RingCentral\Psr7\Response;

final class RouteNotFoundMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response(
            404,
            ['Content-Type' => 'text/html'],
            '<html><head></head><body>Page not found</body></html>'
        );
    }
}
