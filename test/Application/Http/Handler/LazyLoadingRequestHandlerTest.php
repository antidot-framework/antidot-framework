<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Handler;

use Antidot\Application\Http\Handler\LazyLoadingRequestHandler;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LazyLoadingRequestHandlerTest extends TestCase
{
    private const HANDLER_NAME = 'SomeRequestHandler';
    /** @var ServerRequestInterface|MockObject */
    private $request;
    /** @var RequestHandlerInterface|MockObject */
    private $requestHandler;
    /** @var ContainerInterface|MockObject */
    private $container;
    /** @var ResponseInterface */
    private $response;

    public function testItShouldHandleRequestWithLazyLoadedHandlerAndReturnResponse(): void
    {
        $this->givenAServerRequest();
        $this->havingARequestHandler();
        $this->havingAServiceContainer();
        $this->andContainerHavingRequestHandlerConfigured();
        $this->whenRequestIsHandled();
        $this->thenItReturnsAResponse();
    }

    public function testItShouldThrowExceptionWhenContainerDoesntHasHandler(): void
    {
        $this->expectInvalidArgumentException();
        $this->givenAServerRequest();
        $this->havingARequestHandler();
        $this->havingAServiceContainer();
        $this->andContainerHavingDoesntHaveAHandler();
        $this->whenRequestIsHandled();
    }

    private function givenAServerRequest(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    private function havingARequestHandler(): void
    {
        $this->requestHandler = $this->createMock(RequestHandlerInterface::class);
    }

    private function havingAServiceContainer(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    private function andContainerHavingRequestHandlerConfigured(): void
    {
        $this->container
            ->expects($this->once())
            ->method('has')
            ->with(self::HANDLER_NAME)
            ->willReturn(true);
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with(self::HANDLER_NAME)
            ->willReturn($this->requestHandler);
    }

    private function whenRequestIsHandled(): void
    {
        $handler = new LazyLoadingRequestHandler(
            $this->container,
            self::HANDLER_NAME
        );

        $this->response = $handler->handle($this->request);
    }

    private function thenItReturnsAResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->response);
    }

    private function expectInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
    }

    private function andContainerHavingDoesntHaveAHandler(): void
    {
        $this->container
            ->expects($this->once())
            ->method('has')
            ->with(self::HANDLER_NAME)
            ->willReturn(false);
    }
}
