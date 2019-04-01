<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Response;

use Psr\Http\Message\ResponseInterface;
use Throwable;

interface ErrorResponseGenerator
{
    public function __invoke(Throwable $e): ResponseInterface;
}
