<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface Pipeline extends MiddlewareInterface, RequestHandlerInterface
{
    public function pipe(MiddlewareInterface $middleware): void;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
}
