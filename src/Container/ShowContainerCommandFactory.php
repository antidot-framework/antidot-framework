<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Cli\Command\ShowContainer;
use Psr\Container\ContainerInterface;

class ShowContainerCommandFactory
{
    public function __invoke(ContainerInterface $container): ShowContainer
    {
        return new ShowContainer((array)$container->get('config'));
    }
}
