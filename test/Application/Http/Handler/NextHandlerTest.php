<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Handler;

use Antidot\Application\Http\Handler\NextHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

class NextHandlerTest extends TestCase
{
    /** @var SplQueue */
    private $pipeline;
    /** @var ServerRequestInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $request;
    /** @var RequestHandlerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $nextHandler;
    /** @var \Psr\Http\Message\ResponseInterface */
    private $response;

    public function testItShouldHandleRequestAndReturnResponse(): void
    {
        $this->givenAServerRequest();
        $this->havingAPipeline();
        $this->havingNextRequestHandler();
        $this->whenRequestIsHandled();
        $this->thenItReturnsAResponse();
    }

    private function givenAServerRequest(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    private function havingAPipeline(): void
    {
        $this->pipeline = new SplQueue();
    }

    private function havingNextRequestHandler(): void
    {
        $this->nextHandler = $this->createMock(RequestHandlerInterface::class);
    }

    private function whenRequestIsHandled(): void
    {
        $handler = new NextHandler(
            $this->pipeline,
            $this->nextHandler
        );

        $this->response = $handler->handle($this->request);
    }

    private function thenItReturnsAResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->response);
    }
}
