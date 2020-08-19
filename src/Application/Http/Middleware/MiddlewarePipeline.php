<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use Antidot\Application\Http\Handler\NextHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MiddlewarePipeline implements Pipeline
{
    private MiddlewareQueue $middlewareQueue;

    public function __construct(MiddlewareQueue $middlewareQueue)
    {
        $this->middlewareQueue = $middlewareQueue;
    }

    public function pipe(MiddlewareInterface $middleware): void
    {
        $this->middlewareQueue->enqueue($middleware);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->middlewareQueue->dequeue();
        $next = clone $this;

        return $middleware->process($request, $next);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $next = new NextHandler($this->middlewareQueue, $handler);

        return $next->handle($request);
    }
}
