<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use React\Promise\PromiseInterface;
use SplQueue;
use function React\Async\async;
use function React\Async\await;

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
        $next = clone $this;

        return $middleware->process($request, $next);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $next = new NextHandler($this->middlewareQueue, $handler);

        /** @var PromiseInterface $promise */
        $promise = async(fn() => $next->handle($request));

        /** @var ResponseInterface $response */
        $response = await($promise);

        return $response;
    }
}
