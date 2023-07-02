<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyLoadingRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly string $handlerName
    ) {
        $this->assertThatContainerHasHandler($container, $handlerName);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RequestHandlerInterface $handler */
        $handler = $this->container->get($this->handlerName);

        return $handler->handle($request);
    }

    private function assertThatContainerHasHandler(ContainerInterface $container, string $handlerName): void
    {
        if (false === $container->has($handlerName)) {
            throw new InvalidArgumentException(sprintf(
                'The Request Handler is no available in container: %s.',
                $handlerName
            ));
        }
    }
}
