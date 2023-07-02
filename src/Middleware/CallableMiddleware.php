<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Closure;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionFunction;
use ReflectionNamedType;

final class CallableMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Closure $middleware
    ) {
        $this->assertCallableIsValid($middleware);
    }

    /** @psalm-suppress MixedInferredReturnType */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middleware = $this->middleware;

        /** @psalm-suppress MixedReturnStatement */
        return $middleware($request, $handler);
    }

    private function assertCallableIsValid(Closure $middleware): void
    {
        /** @var null|ReflectionNamedType $returnType */
        $returnType = (new ReflectionFunction($middleware))->getReturnType();
        if (null === $returnType || $returnType->getName() !== ResponseInterface::class) {
            throw new InvalidArgumentException(sprintf(
                'Invalid callable given. It must return an instance of %s class.',
                ResponseInterface::class
            ));
        }
    }
}
