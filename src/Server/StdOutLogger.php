<?php

declare(strict_types=1);

namespace Antidot\Framework\Server;

use Psr\Log\LoggerInterface;
use Stringable;
use Symfony\Component\Console\Output\BufferedOutput;

final class StdOutLogger implements LoggerInterface
{
    private const LOG_LEVEL = [
        'EMERGENCY' => 700,
        'ALERT' => 600,
        'CRITICAL' => 500,
        'ERROR' => 400,
        'WARNING' => 300,
        'NOTICE' => 200,
        'INFO' => 100,
        'DEBUG' => 0,
    ];
    private const LOG_COLOR_SCHEMA = [
        'EMERGENCY' => 'red',
        'ALERT' => 'red',
        'CRITICAL' => 'red',
        'ERROR' => 'red',
        'WARNING' => 'yellow',
        'NOTICE' => 'yellow',
        'INFO' => 'cyan',
        'DEBUG' => 'cyan',
    ];

    public function __construct(
        private readonly string $logLevel,
        private readonly BufferedOutput $outputFormat
    ) {
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log('EMERGENCY', $message, $context);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log('ALERT', $message, $context);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log('NOTICE', $message, $context);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * @param string $level
     * @param mixed[] $context
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        if (self::LOG_LEVEL[$this->logLevel] > self::LOG_LEVEL[$level]) {
            return;
        }

        $this->outputFormat->writeln(sprintf(
            '<fg=blue>[</><comment>%s</comment><fg=blue>]</>' .
            '<fg=blue>[</><fg=%s>%s</><fg=blue>]</>: %s',
            (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            self::LOG_COLOR_SCHEMA[$level],
            $level,
            (string)$message
        ));

        fwrite(STDOUT, $this->outputFormat->fetch());
    }
}
