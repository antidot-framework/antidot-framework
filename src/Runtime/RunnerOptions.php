<?php

declare(strict_types=1);

namespace Antidot\Framework\Runtime;

final class RunnerOptions
{
    public function __construct(
        /** @psalm-immutable $debug */
        public bool $debug,
        /** @psalm-immutable $host */
        public string $host = '127.0.0.1',
        /** @psalm-immutable $port */
        public int $port = 8000
    ) {
    }
}
