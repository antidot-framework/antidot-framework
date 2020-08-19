<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Middleware;

use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use Antidot\Application\Http\Middleware\SyncMiddlewareQueue;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

class MiddlewarePipelineTest extends TestCase
{
    /** @var MiddlewarePipeline */
    private $pipeline;
    /** @var MiddlewareInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $middleware;
    /** @var SplQueue */
    private $pipelineCollection;
    /** @var ServerRequestInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $request;
    /** @var RequestHandlerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $handler;
    /** @var ResponseInterface|\Psr\Http\Message\ResponseInterface */
    private $response;

    public function setUp(): void
    {
        $this->pipelineCollection = new SyncMiddlewareQueue();
        $this->pipeline = new MiddlewarePipeline($this->pipelineCollection);
    }

    public function testItShouldPipeMiddlewareInStack(): void
    {
        $this->givenAMiddleware();
        $this->whenPipelineMiddlewarePipeIsCalled();
        $this->thenMiddlewareCollectionIncreasedByOne();
    }

    public function testItShouldHandleServerRequests(): void
    {
        $this->givenAServerRequest();
        $this->havingAPipelineWithAMiddleware();
        $this->whenPipelineHandleIsCalled();
        $this->thenPipelineReturnsValidResponse();
    }

    public function testItShouldProcessServerRequests(): void
    {
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingAPipelineWithAMiddleware();
        $this->whenPipelineProcessIsCalled();
        $this->thenPipelineReturnsValidResponse();
    }

    private function givenAMiddleware(): void
    {
        $this->middleware = $this->createMock(MiddlewareInterface::class);
    }

    private function whenPipelineMiddlewarePipeIsCalled(): void
    {
        $this->pipeline->pipe($this->middleware);
    }

    private function thenMiddlewareCollectionIncreasedByOne(): void
    {
        $this->assertCount(1, $this->pipelineCollection);
    }

    private function havingAPipelineWithAMiddleware(): void
    {
        $this->givenAMiddleware();
        $this->whenPipelineMiddlewarePipeIsCalled();
    }

    private function givenAServerRequest(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    private function givenARequestHandler()
    {
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    private function whenPipelineProcessIsCalled(): void
    {
        $this->response = $this->pipeline->process($this->request, $this->handler);
    }

    private function thenPipelineReturnsValidResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->response);
    }

    private function whenPipelineHandleIsCalled(): void
    {
        $this->response = $this->pipeline->handle($this->request);
    }
}
