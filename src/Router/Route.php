<?php

declare(strict_types=1);

namespace Antidot\Framework\Router;

use Closure;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

final class Route
{
    /**
     * @param array<string> $method
     * @param array<
     *     (callable():ResponseInterface)|MiddlewareInterface|RequestHandlerInterface|string
     * >  $pipeline
     */
    public function __construct(
        /** @psalm-immutable $method */
        public array $method,
        /** @psalm-immutable $name */
        public string $name,
        /** @psalm-immutable $path */
        public string $path,
        /** @psalm-immutable $pipeline */
        public array $pipeline
    ) {
    }
}
