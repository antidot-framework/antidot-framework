<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Handler\CallableRequestHandler;
use Antidot\Application\Http\Handler\LazyLoadingRequestHandler;
use Closure;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function get_class;
use function is_object;
use function is_string;
use function sprintf;
use function var_export;

class RequestHandlerFactory
{
    public const INVALID_HANDLER_MESSAGE = 'Invalid handler %s given. It must be an instance of '
    . 'Psr\Http\Server\RequestHandlerInterface, an existing container service name, or an anonymous function.';
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ResponseInterface|string|Closure $handler
     * @return RequestHandlerInterface
     * @throws InvalidArgumentException
     */
    public function create($handler): RequestHandlerInterface
    {
        if ($handler instanceof RequestHandlerInterface) {
            return $handler;
        }

        if (is_string($handler)) {
            return new LazyLoadingRequestHandler($this->container, $handler);
        }

        if ($this->isClosure($handler)) {
            /** @var Closure $handler */
            return new CallableRequestHandler($handler);
        }

        throw new InvalidArgumentException(sprintf(
            self::INVALID_HANDLER_MESSAGE,
            $this->getHandlerAsString($handler)
        ));
    }

    /**
     * @param mixed $callable
     * @return bool
     */
    private function isClosure($callable): bool
    {
        return is_object($callable) && ($callable instanceof Closure);
    }

    /**
     * @param mixed $handler
     * @return string
     */
    private function getHandlerAsString($handler): string
    {
        if (is_object($handler)) {
            return get_class($handler);
        }

        return var_export($handler, true);
    }
}
