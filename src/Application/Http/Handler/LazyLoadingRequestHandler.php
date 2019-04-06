<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Handler;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LazyLoadingRequestHandler implements RequestHandlerInterface
{
    /** @var ContainerInterface */
    private $container;
    /** @var string */
    private $handlerName;

    public function __construct(ContainerInterface $container, string $handlerName)
    {
        if (false === $container->has($handlerName)) {
            throw new InvalidArgumentException('Invalid handler name given.');
        }
        $this->container = $container;
        $this->handlerName = $handlerName;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->container->get($this->handlerName);

        return $handler->handle($request);
    }
}
