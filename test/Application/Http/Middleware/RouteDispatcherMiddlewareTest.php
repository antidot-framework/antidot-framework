<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Middleware;

use Antidot\Application\Http\Middleware\PipedRoute;
use Antidot\Application\Http\Middleware\RouteDispatcherMiddleware;
use Antidot\Application\Http\Router;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteDispatcherMiddlewareTest extends TestCase
{
    /** @var ServerRequestInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $request;
    /** @var RequestHandlerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $handler;
    /** @var Router|\PHPUnit\Framework\MockObject\MockObject */
    private $router;
    /** @var ResponseInterface */
    private $response;

    public function testItShouldDispatchRouteWhenExist(): void
    {
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingARouter();
        $this->whenRouteExist();
        $this->andRequestIsHandled();
        $this->thenItReturnsAResponse();
    }

    public function testItShouldProcessNextMiddlewareWhenRouteNotExist(): void
    {
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingARouter();
        $this->whenRouteDoesNotExist();
        $this->andRequestIsHandled();
        $this->thenItReturnsAResponse();
    }

    private function givenAServerRequest(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    private function givenARequestHandler(): void
    {
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    private function havingARouter(): void
    {
        $this->router = $this->createMock(Router::class);
    }

    private function whenRouteExist(): void
    {
        $route = $this->createMock(PipedRoute::class);
        $route
            ->expects($this->once())
            ->method('isFail')
            ->willReturn(false);
        $this->router
            ->expects($this->once())
            ->method('match')
            ->willReturn($route);
        $route->expects($this->once())
            ->method('process')
            ->with($this->request, $this->handler);
    }

    private function andRequestIsHandled(): void
    {
        $middleware = new RouteDispatcherMiddleware($this->router);

        $this->response = $middleware->process($this->request, $this->handler);
    }

    private function thenItReturnsAResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->response);
    }

    private function whenRouteDoesNotExist(): void
    {
        $route = $this->createMock(PipedRoute::class);
        $route
            ->expects($this->once())
            ->method('isFail')
            ->willReturn(true);
        $this->router
            ->expects($this->once())
            ->method('match')
            ->willReturn($route);
        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->request);
    }
}
