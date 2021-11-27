<?php

declare(strict_types=1);

namespace Antidot\Framework\Cli;

use Psr\Container\ContainerInterface;

final class ServeCommandFactory
{
    public function __invoke(ContainerInterface $container): ServeCommand
    {
        /** @var array{server: array<array-key, int>} $globalConfig */
        $globalConfig = $container->get('config');
        /** @var array{workers: int} $config */
        $config = $globalConfig['server'];

        return new ServeCommand($config['workers']);
    }
}
