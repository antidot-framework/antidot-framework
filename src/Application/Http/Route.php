<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

interface Route
{
    public function name(): string;
    public function path(): string;
    public function method(): array;
    public function pipeline(): array;
}
