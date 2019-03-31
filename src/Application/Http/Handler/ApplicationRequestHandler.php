<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApplicationRequestHandler implements RequestHandlerInterface
{
    private $handler;
    private $pipeline;

    private function __construct(Pipeline $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    public static function fromCallable(Pipeline $pipeline, callable $callable): self
    {
        $self = new self($pipeline);
        $self->handler = new CallableRequestHandler($callable);

        return $self;
    }

    public static function fromRequestHandle(Pipeline $pipeline, RequestHandlerInterface $handler): self
    {
        $self = new self($pipeline);
        $self->handler = $handler;

        return $self;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->pipeline->process($request, $this->handler);
    }
}
