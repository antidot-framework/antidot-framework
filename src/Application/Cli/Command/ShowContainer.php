<?php

declare(strict_types=1);

namespace Antidot\Application\Cli\Command;

use App\Container\Config\AppConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ShowContainer extends Command
{
    public const NAME = 'config:show:container';
    private $config;

    public function __construct(array $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Show all available services inner container.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);

        $table->setHeaders(['service', 'implementation']);
        $table->addRow(['<info>Invokables</info>']);
        $table->addRow(new TableSeparator());
        $invokables = $this->config['dependencies']['invokables'] ?? [];
        foreach ($invokables as $invokable => $instance) {
            $table->addRow([$invokable, $instance]);
        }
        $table->addRow(new TableSeparator());
        $table->addRow(['<info>Factories</info>']);
        $table->addRow(new TableSeparator());
        $factories = $this->config['dependencies']['factories'] ?? [];
        foreach ($factories as $key => $factory) {
            $table->addRow([$key, is_array($factory) ? $factory[0].'::'.$factory[1] : $factory]);
        }
        $table->addRow(new TableSeparator());
        $table->addRow(['<info>Aliases</info>']);
        $table->addRow(new TableSeparator());
        $aliases = $this->config['dependencies']['aliases'] ?? [];
        foreach ($aliases as $key => $alias) {
            $table->addRow([$key, $alias]);
        }
        $table->addRow(new TableSeparator());
        $table->addRow(['<info>Conditionals</info>']);
        $table->addRow(new TableSeparator());
        $conditionals = $this->config['dependencies']['conditionals'] ?? [];
        foreach ($conditionals as $key => $conditional) {
            $table->addRow([$key, $conditional['class']]);
            foreach ($conditional['arguments'] as $index => $argument) {
                $table->addRow([
                    '',
                    ' - '.$index.'::'.(\is_string($argument) ? $argument : \gettype($argument)),
                ]);
            }
        }

        $table->render();
    }
}
