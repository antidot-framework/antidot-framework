<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Handler;

use Closure;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionFunction;

final class CallableRequestHandler implements RequestHandlerInterface
{
    /** @var Closure  */
    private $handler;

    public function __construct(Closure $handler)
    {
        $this->assertCallableIsValid($handler);
        $this->handler = $handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->handler;

        return $handler($request);
    }

    private function assertCallableIsValid(Closure $handler): void
    {
        $returnType = (new ReflectionFunction($handler))->getReturnType();
        if (null === $returnType || $returnType->getName() !== ResponseInterface::class) {
            throw new InvalidArgumentException('Invalid callable given.');
        }
    }
}
