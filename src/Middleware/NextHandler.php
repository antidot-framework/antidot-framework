<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

final class NextHandler implements RequestHandlerInterface
{
    /**
     * @param SplQueue<MiddlewareInterface> $queue
     */
    public function __construct(
        private SplQueue $queue,
        private RequestHandlerInterface $handler
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return $this->handler->handle($request);
        }

        $middleware = $this->queue->dequeue();

        return $middleware->process($request, $this);
    }
}
