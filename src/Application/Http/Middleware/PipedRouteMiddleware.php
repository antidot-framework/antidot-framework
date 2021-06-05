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
    private Pipeline $pipeline;
    private bool $isFail;
    /** @var array<string, mixed> */
    private array $attributes;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(Pipeline $pipeline, bool $isFail, array $attributes)
    {
        $this->pipeline = $pipeline;
        $this->isFail = $isFail;
        $this->attributes = $attributes;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isFail()) {
            throw RouteNotFound::withPath($request->getRequestTarget());
        }

        /**
         * @var mixed $value
         */
        foreach ($this->attributes as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }

        return $this->pipeline->handle($request);
    }

    public function isFail(): bool
    {
        return $this->isFail;
    }
}
