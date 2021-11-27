<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MethodNotAllowedMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response(
            403,
            ['Content-Type' => 'text/html'],
            '<html><head></head><body>Method Not Allowed</body></html>'
        );
    }
}
