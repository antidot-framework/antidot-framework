<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Handler;

use Antidot\Application\Http\Middleware\MiddlewareQueue;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

final class NextHandler implements RequestHandlerInterface
{
    private MiddlewareQueue $queue;
    private RequestHandlerInterface $handler;

    public function __construct(MiddlewareQueue $queue, RequestHandlerInterface $requestHandler)
    {
        $this->queue = $queue;
        $this->handler = $requestHandler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return $this->handler->handle($request);
        }

        /** @var MiddlewareInterface $middleware */
        $middleware = $this->queue->dequeue();

        return $middleware->process($request, $this);
    }
}
