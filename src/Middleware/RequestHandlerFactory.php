<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_string;

class RequestHandlerFactory
{
    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function create(RequestHandlerInterface|Closure|string $handler): RequestHandlerInterface
    {
        if ($handler instanceof RequestHandlerInterface) {
            return $handler;
        }

        if (is_string($handler)) {
            return new LazyLoadingRequestHandler($this->container, $handler);
        }

        return new CallableRequestHandler($handler);
    }
}
