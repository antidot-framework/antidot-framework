<?php

declare(strict_types=1);

namespace Antidot\Test\Framework\Middleware;

use Antidot\Framework\Middleware\LazyLoadingRequestHandler;
use Psr\Container\ContainerInterface;
use Antidot\Framework\Middleware\RequestHandlerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestHandlerFactoryTest extends TestCase
{
    public function testItShouldReturnTheRequestHandlerAsIs(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new RequestHandlerFactory($container);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $requestHandler = $factory->create($handler);
        self::assertSame($handler, $requestHandler);
    }

    public function testItShouldReturnLazyLoadedRequestHandler(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with('some_handler')
            ->willReturn(true);
        $container
            ->method('get')
            ->with('some_handler')
            ->willReturn($this->createMock(RequestHandlerInterface::class));

        $factory = new RequestHandlerFactory($container);
        $requestHandler = $factory->create('some_handler');
        self::assertInstanceOf(LazyLoadingRequestHandler::class, $requestHandler);
    }
}
