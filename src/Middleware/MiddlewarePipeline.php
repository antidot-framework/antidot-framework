<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

final class MiddlewarePipeline implements Pipeline
{
    /**
     * @param SplQueue<MiddlewareInterface> $middlewareQueue
     */
    public function __construct(
        private SplQueue $middlewareQueue
    ) {
    }

    public function pipe(MiddlewareInterface $middleware): void
    {
        $this->middlewareQueue->enqueue($middleware);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->middlewareQueue->dequeue();
        $next = new self($this->middlewareQueue);

        return $middleware->process($request, $next);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $next = new NextHandler($this->middlewareQueue, $handler);

        return $next->handle($request);
    }
}
