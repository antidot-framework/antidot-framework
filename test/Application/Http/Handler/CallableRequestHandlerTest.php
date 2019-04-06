<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Handler;

use Antidot\Application\Http\Handler\CallableRequestHandler;
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

    public function testItShouldHandleRequestWithCallableAndReturnResponse(): void
    {
        $this->givenAServerRequest();
        $this->havingACallableRequestHandler();
        $this->whenRequestIsHandled();
        $this->thenItReturnsAResponse();
    }

    private function givenAServerRequest(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    private function havingACallableRequestHandler(): void
    {
        $this->handlerChecker = $this->createMock(RequestHandlerInterface::class);
        $this->handlerChecker
            ->expects($this->once())
            ->method('handle')
            ->with($this->request);
    }

    private function whenRequestIsHandled(): void
    {
        $handler = new CallableRequestHandler(function (ServerRequestInterface $request): ResponseInterface {
            return $this->handlerChecker->handle($request);
        });

        $this->response = $handler->handle($this->request);
    }

    private function thenItReturnsAResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->response);
    }
}
