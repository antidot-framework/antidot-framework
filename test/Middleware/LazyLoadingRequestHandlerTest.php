<?php

namespace Antidot\Test\Framework\Middleware;

use Antidot\Framework\Middleware\LazyLoadingRequestHandler;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyLoadingRequestHandlerTest extends TestCase
{
    public function testItShouldHandleRequestAndReturnResponse(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('has')
            ->with('foo')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('foo')
            ->willReturn($this->createMock(RequestHandlerInterface::class));
        $handler = new LazyLoadingRequestHandler($container, 'foo');
        $response = $handler->handle($this->createMock(ServerRequestInterface::class));
        self::assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testItShouldThrowExceptionWhenRequestHandlerIsNotPresentInContainer(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('has')
            ->with('foo')
            ->willReturn(false);
        new LazyLoadingRequestHandler($container, 'foo');
    }
}
