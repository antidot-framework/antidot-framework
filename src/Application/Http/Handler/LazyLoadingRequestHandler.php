<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Handler;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyLoadingRequestHandler implements RequestHandlerInterface
{
    private ContainerInterface $container;
    private string $handlerName;

    public function __construct(ContainerInterface $container, string $handlerName)
    {
        $this->assertThatContainerHasHandler($container, $handlerName);
        $this->container = $container;
        $this->handlerName = $handlerName;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->container->get($this->handlerName);

        return $handler->handle($request);
    }

    private function assertThatContainerHasHandler(ContainerInterface $container, string $handlerName): void
    {
        if (false === $container->has($handlerName)) {
            throw new InvalidArgumentException(sprintf('Invalid handler name given %s.', $handlerName));
        }
    }
}
