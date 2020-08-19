<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use Countable;
use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareQueue extends Countable
{
    public function enqueue(MiddlewareInterface $middleware): void;
    public function dequeue(): MiddlewareInterface;
    public function isEmpty(): bool;
}
