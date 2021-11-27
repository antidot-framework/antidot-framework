<?php

namespace Antidot\Test\Framework\Middleware;

use Psr\Container\ContainerInterface;
use Antidot\Framework\Middleware\LazyLoadingMiddleware;
use PHPUnit\Framework\TestCase;

class LazyLoadingMiddlewareTest extends TestCase
{
    public function testItShouldThrowExceptionWhenMiddlewareIsNotPresentInContainer(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('has')
            ->with('foo')
            ->willReturn(false);
        new LazyLoadingMiddleware($container, 'foo');
    }
}
