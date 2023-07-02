<?php

declare(strict_types=1);

namespace Antidot\Framework\Server;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class StdOutLoggerFactory
{
    public function __invoke(ContainerInterface $container): StdOutLogger
    {
        /** @var array{log_level: string} $config */
        $config = $container->get('config');
        $logLevel = $config['log_level'];
        $outputFormat = new BufferedOutput(
            OutputInterface::VERBOSITY_NORMAL,
            true
        );

        return new StdOutLogger($logLevel, $outputFormat);
    }
}
