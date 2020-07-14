<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use Antidot\Application\Http\Handler\NextHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

final class MiddlewarePipeline implements Pipeline
{
    /** @var SplQueue<MiddlewareInterface>  */
    private SplQueue $middlewareCollection;

    /**
     * @param SplQueue<MiddlewareInterface> $middlewareCollection
     */
    public function __construct(SplQueue $middlewareCollection)
    {
        $this->middlewareCollection = $middlewareCollection;
    }

    public function pipe(MiddlewareInterface $middleware): void
    {
        $this->middlewareCollection->enqueue($middleware);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var MiddlewareInterface $middleware */
        $middleware = $this->middlewareCollection->dequeue();
        $next = clone $this;

        return $middleware->process($request, $next);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $next = new NextHandler($this->middlewareCollection, $handler);

        return $next->handle($request);
    }
}
