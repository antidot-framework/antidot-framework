<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Handler;

use Closure;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionFunction;
use ReflectionNamedType;

final class CallableRequestHandler implements RequestHandlerInterface
{
    private Closure $handler;

    public function __construct(Closure $handler)
    {
        $this->assertCallableIsValid($handler);
        $this->handler = $handler;
    }

    /** @psalm-suppress MixedInferredReturnType */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->handler;

        /** @psalm-suppress MixedReturnStatement */
        return $handler($request);
    }

    private function assertCallableIsValid(Closure $handler): void
    {
        /** @var null|ReflectionNamedType $returnType */
        $returnType = (new ReflectionFunction($handler))->getReturnType();
        if (null === $returnType || $returnType->getName() !== ResponseInterface::class) {
            throw new InvalidArgumentException('Invalid callable given.');
        }
    }
}
