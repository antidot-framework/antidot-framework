<?php

declare(strict_types=1);

namespace Antidot\Framework\Runtime;

use Antidot\Framework\Application;
use Symfony\Component\Runtime\GenericRuntime;
use Symfony\Component\Runtime\RunnerInterface;

final class AntidotRuntime extends GenericRuntime
{
    private int $port;
    private bool $debug;
    private string $host;

    /**
     * @param array{host: string, port: int, debug: bool} $options
     */
    public function __construct(array $options)
    {
        $this->host = $options['host'];
        $this->port = $options['port'];
        $this->debug = $options['debug'];
        parent::__construct($options);
    }

    public function getRunner(?object $application): RunnerInterface
    {
        if ($application instanceof Application) {
            return new AntidotRunner($application, new RunnerOptions($this->debug, $this->host, $this->port));
        }

        return parent::getRunner($application);
    }
}
