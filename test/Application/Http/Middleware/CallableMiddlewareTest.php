<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Middleware;

use Antidot\Application\Http\Middleware\CallableMiddleware;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableMiddlewareTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;
    /** @var RequestHandlerInterface|MockObject */
    private $handler;
    /** @var \Closure */
    private $callableMiddleware;
    /** @var ResponseInterface */
    private $response;

    public function testItShouldProcessCallableMiddleware(): void
    {
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingCallableMiddleware();
        $this->whenRequestIsProcessedThoughPipeline();
        $this->thenItReturnsAResponse();
    }

    /**
     * @dataProvider getInvalidCallables
     */
    public function testItShouldThrowExceptionWhenCallableIsNotValidMiddleware($callable): void
    {
        $this->expectsInvalidArgumentException();
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingCallableThatNotFitMiddlewareRequirements($callable);
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

    private function havingCallableMiddleware(): void
    {
        $middlewareChecker = $this->createMock(MiddlewareInterface::class);
        $middlewareChecker
            ->expects($this->once())
            ->method('process')
            ->with($this->request, $this->handler);

        $this->callableMiddleware = function (
            ServerRequestInterface $request,
            RequestHandlerInterface $handler
        ) use ($middlewareChecker): ResponseInterface {
            return $middlewareChecker->process($request, $handler);
        };
    }

    private function whenRequestIsProcessedThoughPipeline(): void
    {
        $middleware = new CallableMiddleware($this->callableMiddleware);
        $this->response = $middleware->process($this->request, $this->handler);
    }

    private function thenItReturnsAResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->response);
    }

    private function expectsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
    }

    private function havingCallableThatNotFitMiddlewareRequirements(callable $callable): void
    {
        $this->callableMiddleware = $callable;
    }

    public function getInvalidCallables(): array
    {
        return [
            [function () {}],
        ];
    }
}
