<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Middleware;

use Antidot\Application\Http\Middleware\LazyLoadingMiddleware;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LazyLoadingMiddlewareTest extends TestCase
{
    private const MIDDLEWARE_NAME = 'some.middleware';
    /** @var ServerRequestInterface|MockObject */
    private $request;
    /** @var RequestHandlerInterface|MockObject */
    private $handler;
    /** @var ContainerInterface|MockObject */
    private $container;
    /** @var ResponseInterface */
    private $response;

    public function testItShouldProcessLazyLoadingMiddleware(): void
    {
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingAContainer();
        $this->havingAMiddlewareInContainer();
        $this->whenRequestIsProcessedThoughPipeline();
        $this->thenItReturnsAResponse();
    }

    public function testItShouldThrowExceptionWhenContainerDoesntHaveMiddleware(): void
    {
        $this->expectInvalidArgumentException();
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingAContainerWithoutMiddleware();
        $this->whenRequestIsProcessedThoughPipeline();
    }

    private function givenAServerRequest(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    private function givenARequestHandler(): void
    {
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    private function havingAContainer(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    private function havingAMiddlewareInContainer(): void
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->expects($this->once())
            ->method('process')
            ->with($this->request, $this->handler);
        $this->container
            ->expects($this->once())
            ->method('has')
            ->with(self::MIDDLEWARE_NAME)
            ->willReturn(true);
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with(self::MIDDLEWARE_NAME)
            ->willReturn($middleware);
    }

    private function whenRequestIsProcessedThoughPipeline(): void
    {
        $middleware = new LazyLoadingMiddleware($this->container, self::MIDDLEWARE_NAME);
        $this->response = $middleware->process($this->request, $this->handler);
    }

    private function thenItReturnsAResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->response);
    }

    private function expectInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
    }

    private function havingAContainerWithoutMiddleware(): void
    {
        $this->havingAContainer();
        $this->container
            ->expects($this->once())
            ->method('has')
            ->with(self::MIDDLEWARE_NAME)
            ->willReturn(false);
    }
}
