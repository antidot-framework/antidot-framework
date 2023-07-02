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
        public readonly array $method,
        public readonly string $name,
        public readonly string $path,
        public readonly array $pipeline
    ) {
    }
}
