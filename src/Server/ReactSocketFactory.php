<?php

declare(strict_types=1);

namespace Antidot\Framework\Server;

use Psr\Container\ContainerInterface;
use React\Socket\SocketServer;

final class ReactSocketFactory
{
    public function __invoke(ContainerInterface $container): SocketServer
    {
        /** @var array{server: array{host: string, port: int, workers: int}} $globalConfig */
        $globalConfig = $container->get('config');
        $serverConfig = $globalConfig['server'];

        return new SocketServer(
            sprintf('%s:%d', $serverConfig['host'], $serverConfig['port']),
            ['tcp' => ['so_reuseport' => 1 <= $serverConfig['workers']]]
        );
    }
}
