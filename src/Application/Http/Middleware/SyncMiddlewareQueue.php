<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use \SplQueue;

class SyncMiddlewareQueue implements MiddlewareQueue
{
    /**
     * @var SplQueue<MiddlewareInterface>
     */
    private SplQueue $queue;

    public function __construct()
    {
        /** @psalm-suppress MixedPropertyTypeCoercion */
        $this->queue = new SplQueue();
    }

    public function enqueue(MiddlewareInterface $middleware): void
    {
        $this->queue->enqueue($middleware);
    }

    public function dequeue(): MiddlewareInterface
    {
        /** @var MiddlewareInterface $middleware */
        $middleware = $this->queue->dequeue();

        return $middleware;
    }

    public function count(): int
    {
        return $this->queue->count();
    }

    public function isEmpty(): bool
    {
        return $this->queue->isEmpty();
    }
}
