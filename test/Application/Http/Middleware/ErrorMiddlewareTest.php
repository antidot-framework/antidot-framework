<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Middleware;

use Antidot\Application\Http\Middleware\ErrorMiddleware;
use function dump;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ErrorMiddlewareTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;
    /** @var RequestHandlerInterface|MockObject */
    private $handler;
    /** @var bool */
    private $debug;
    /** @var ResponseInterface */
    private $response;

    public function testItShouldProcessMiddlewareWhenDebugModelIsDisabledAndDoesNotContainErrors(): void
    {
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingDebugModeDisabled();
        $this->whenMiddlewareIsProcessed();
        $this->thenResponseShouldHaveExpectedStatusCode();
    }

    public function testItShouldProcessMiddlewareWhenDebugModelIsEnabledAndDoesNotContainErrors(): void
    {
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingDebugModeEnabled();
        $this->whenMiddlewareIsProcessed();
        $this->thenResponseShouldHaveExpectedStatusCode();
    }

    public function testItShouldProcessResponseWhenDebugModelIsDisabledAndContainErrors(): void
    {
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingDebugModeDisabled();
        $this->havingErrors();
        $this->whenMiddlewareIsProcessed();
        $this->thenResponseShouldHaveExpectedErrorStatusCode();
    }

    public function testItShouldProcessResponseWhenDebugModelIsEnabledAndContainErrors(): void
    {
        $this->givenAServerRequest();
        $this->givenARequestHandler();
        $this->havingDebugModeEnabled();
        $this->havingErrors();
        $this->whenMiddlewareIsProcessed();
        $this->thenResponseShouldHaveExpectedErrorStatusCode();
    }

    private function givenAServerRequest(): void
    {
        $this->request = $this->createConfiguredMock(ServerRequestInterface::class, [
            'getHeader' => ['application/json']
        ]);
    }

    private function givenARequestHandler(): void
    {
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    private function havingDebugModeDisabled(): void
    {
        $this->debug = false;
    }

    private function whenMiddlewareIsProcessed(): void
    {
        $middleware = new ErrorMiddleware($this->debug);
        $this->response = $middleware->process($this->request, $this->handler);
    }

    private function thenResponseShouldHaveExpectedStatusCode(): void
    {
        $this->assertNotEquals(500, $this->response->getStatusCode());
    }

    private function havingDebugModeEnabled(): void
    {
        $this->debug = true;
    }

    private function havingErrors(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->request)
            ->willThrowException(new InvalidArgumentException());
    }

    private function thenResponseShouldHaveExpectedErrorStatusCode(): void
    {
        $this->assertEquals(500, $this->response->getStatusCode());
    }
}
