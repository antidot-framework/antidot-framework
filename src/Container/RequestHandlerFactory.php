<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Handler\CallableRequestHandler;
use Antidot\Application\Http\Handler\LazyLoadingRequestHandler;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_callable;
use function is_string;

class RequestHandlerFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create($handler): RequestHandlerInterface
    {
        if ($handler instanceof RequestHandlerInterface) {
            return $handler;
        }

        if (is_string($handler)) {
            return new LazyLoadingRequestHandler($this->container, $handler);
        }

        if (is_callable($handler)) {
            return new CallableRequestHandler($handler);
        }

        throw new InvalidArgumentException('Invalid handler given.');
    }
}
