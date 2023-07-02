<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PipedRouteMiddleware implements MiddlewareInterface
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        private readonly Pipeline $pipeline,
        private readonly array $attributes
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var mixed $value
         */
        foreach ($this->attributes as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }

        return $this->pipeline->handle($request);
    }
}
