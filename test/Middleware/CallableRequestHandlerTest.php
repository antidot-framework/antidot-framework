<?php

declare(strict_types=1);

namespace Antidot\Test\Framework\Middleware;

use Antidot\Framework\Middleware\CallableRequestHandler;
use PHPUnit\Framework\TestCase;

class CallableRequestHandlerTest extends TestCase
{
    public function testItShouldThrowExceptionWithInvalidCallableRequestHandler(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new CallableRequestHandler(
            function () {}
        );
    }

}
