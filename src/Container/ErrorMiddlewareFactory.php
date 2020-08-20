<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Middleware\ErrorMiddleware;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

use function array_key_exists;
use function gettype;
use function is_bool;
use function sprintf;

class ErrorMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        /** @var array<string, mixed> $config */
        $config = $container->get('config');

        return new ErrorMiddleware($this->hasDebug($config));
    }

    /**
     * @param array<string, mixed> $config
     * @return bool
     */
    private function hasDebug(array $config): bool
    {
        return array_key_exists('debug', $config) ? (bool) $config['debug'] : false;
    }
}
