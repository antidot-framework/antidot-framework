<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use Antidot\Application\Http\Exception\RouteNotFound;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PipedRouteMiddleware implements MiddlewareInterface
{
    /** @var Pipeline */
    private $pipeline;
    /** @var bool */
    private $isFail;

    public function __construct(Pipeline $pipeline, bool $isFail)
    {
        $this->pipeline = $pipeline;
        $this->isFail = $isFail;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isFail) {
            throw RouteNotFound::withPath($request->getRequestTarget());
        }

        return $this->pipeline->handle($request);
    }

    public function isFail(): bool
    {
        return $this->isFail;
    }
}
