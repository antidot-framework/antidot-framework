<?php

declare(strict_types=1);

namespace Antidot\Framework\Cli;

use React\ChildProcess\Process;
use React\EventLoop\Loop;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ServeCommand extends Command
{
    public const NAME = 'serve';

    public function __construct(
        private int $workersNumber
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Start running HTTP server');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        for ($worker = 1; $this->workersNumber >= $worker; $worker++) {
            $process = new Process('php public/index.php');

            $process->start();
            $output->writeLn('<fg=blue>php</> public/index.php <fg=blue>' . $worker . '</>');

            if (null === $process->stdout) {
                throw new \RuntimeException();
            }

            $process->stdout->on('data', function (string $chunk) use ($output) {
                $output->write($chunk);
            });

            $process->on('exit', function (int $exitCode) use ($output) {
                $output->writeLn('Process exited with code ' . $exitCode);
            });
        }

        Loop::get()->run();

        return Command::SUCCESS;
    }
}
