<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

use Antidot\Application\Http\Middleware\PipedRoute;
use Psr\Http\Message\ServerRequestInterface;

interface Router
{
    public function append(Route $route): void;
    public function match(ServerRequestInterface $request): PipedRoute;
}
