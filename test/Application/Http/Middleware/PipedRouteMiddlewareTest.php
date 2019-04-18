<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Middleware;

use Antidot\Application\Http\Exception\RouteNotFound;
use Antidot\Application\Http\Middleware\PipedRouteMiddleware;
use Antidot\Application\Http\Middleware\Pipeline;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PipedRouteMiddlewareTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;
    /** @var RequestHandlerInterface|MockObject */
    private $handler;
    /** @var Pipeline|MockObject */
    private $pipeline;
    /** @var bool */
    private $routeDoesNoExist;
    /** @var ResponseInterface */
    private $response;

    public function testItShouldProcessAMiddlewarePipelineConfiguredForARoute(): void
    {
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingAPipeline();
        $this->havingAMatchingRoute();
        $this->whenPipedRouteIsProcessed();
        $this->thenItReturnsAResponse();
    }

    public function testItShouldThrowRouteNotFoundExceptionWhenThereIsNotMatchingRoute(): void
    {
        $this->expectsRouteNotFoundException();
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingAPipeline();
        $this->havingANotMatchingRoute();
        $this->whenPipedRouteIsProcessed();
    }

    private function givenAServerRequest(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    private function givenARequestHandler(): void
    {
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    private function havingAPipeline(): void
    {
        $this->pipeline = $this->createMock(Pipeline::class);
    }

    private function havingAMatchingRoute(): void
    {
        $this->routeDoesNoExist = false;
    }

    private function whenPipedRouteIsProcessed(): void
    {
        $pipedRoute = new PipedRouteMiddleware(
            $this->pipeline,
            $this->routeDoesNoExist,
            []
        );

        $this->response = $pipedRoute->process($this->request, $this->handler);
    }

    private function thenItReturnsAResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->response);
    }

    private function expectsRouteNotFoundException(): void
    {
        $this->expectException(RouteNotFound::class);
    }

    private function havingANotMatchingRoute(): void
    {
        $this->routeDoesNoExist = true;
        $this->request
            ->expects($this->once())
            ->method('getRequestTarget')
            ->willReturn('/fake-path');
    }
}
