<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Handler;

use Antidot\Application\Http\Handler\CallableRequestHandler;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableRequestHandlerTest extends TestCase
{

    /** @var ServerRequestInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $request;
    /** @var RequestHandlerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $handlerChecker;
    /** @var ResponseInterface */
    private $response;
    /** @var \Closure */
    private $callable;

    public function testItShouldHandleRequestWithCallableAndReturnResponse(): void
    {
        $this->givenAServerRequest();
        $this->havingACallableRequestHandler();
        $this->whenRequestIsHandled();
        $this->thenItReturnsAResponse();
    }

    public function testItShouldThrowInvalidArgumentExceptionWhenCallableIsNotValid(): void
    {
        $this->expectsInvalidArgumentException();
        $this->givenAServerRequest();
        $this->havingInvalidCallableRequestHandler();
        $this->whenRequestIsHandled();
    }

    private function givenAServerRequest(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    private function havingACallableRequestHandler(): void
    {
        $handlerChecker = $this->createMock(RequestHandlerInterface::class);
        $handlerChecker
            ->expects($this->once())
            ->method('handle')
            ->with($this->request);
        $this->callable = static function (ServerRequestInterface $request) use ($handlerChecker): ResponseInterface {
            return $handlerChecker->handle($request);
        };
    }

    private function whenRequestIsHandled(): void
    {
        $handler = new CallableRequestHandler($this->callable);

        $this->response = $handler->handle($this->request);
    }

    private function thenItReturnsAResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->response);
    }

    private function expectsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
    }

    private function havingInvalidCallableRequestHandler(): void
    {
        $this->callable = function () {};
    }
}
