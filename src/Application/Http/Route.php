<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

interface Route
{
    public function name(): string;
    public function path(): string;

    /**
     * @return array<string>
     */
    public function method(): array;

    /**
     * @return array<string>
     */
    public function pipeline(): array;
}
