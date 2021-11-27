<?php

declare(strict_types=1);

namespace Antidot\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

interface Router
{
    public function append(Route $route): void;
    public function match(ServerRequestInterface $request): MiddlewareInterface;
}
