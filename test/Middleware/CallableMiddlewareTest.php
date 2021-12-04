<?php

declare(strict_types=1);

namespace Antidot\Test\Framework\Middleware;

use Antidot\Framework\Middleware\CallableMiddleware;
use PHPUnit\Framework\TestCase;

final class CallableMiddlewareTest extends TestCase
{
    public function testItShouldThrowExceptionWithInvalidCallableMiddleware(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new CallableMiddleware(
            function () {}
        );
    }
}
