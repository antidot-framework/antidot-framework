<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionFunction;

final class CallableMiddleware implements MiddlewareInterface
{
    /** @var callable */
    private $middleware;

    public function __construct(callable $middleware)
    {
        $this->assertCallableIsValid($middleware);
        $this->middleware = $middleware;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middleware = $this->middleware;

        return $middleware($request, $handler);
    }

    private function assertCallableIsValid($middleware): void
    {
        $returnType = (new ReflectionFunction($middleware))->getReturnType();
        if (null === $returnType || $returnType->getName() !== ResponseInterface::class) {
            throw new InvalidArgumentException('Invalid callable given.');
        }
    }
}
