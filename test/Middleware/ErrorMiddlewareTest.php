<?php

declare(strict_types=1);

namespace Antidot\Test\Framework\Middleware;

use Antidot\Framework\Application;
use Antidot\Framework\Middleware\ErrorMiddleware;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ErrorMiddlewareTest extends TestCase
{
    public function testItShouldProcessMiddlewareAndCaptureWhenErrorOcurredWithoutDebugEnabled(): void
    {
        $request = $this->getRequest();
        $handler = new class implements RequestHandlerInterface
        {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new \InvalidArgumentException();
            }
        };

        $errorMiddleware = new ErrorMiddleware(false);
        $response = $errorMiddleware->process($request, $handler);

        self::assertSame(500, $response->getStatusCode());
    }

    public function testItShouldProcessMiddlewareAndCaptureWhenErrorOcurredWithDebugEnabled(): void
    {
        $request = $this->getRequest();
        $handler = new class implements RequestHandlerInterface
        {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200, [], ['foo' => 'bar']);
            }
        };

        $errorMiddleware = new ErrorMiddleware(true);
        $response = $errorMiddleware->process($request, $handler);

        self::assertSame(500, $response->getStatusCode());
    }

    public function testItShouldProcessMiddlewareAndCaptureWhenErrorOcurredWithDebugEnabledAndXApplication(): void
    {
        $request = $this->getRequest();
        $handler = new class implements RequestHandlerInterface
        {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $_SERVER['X-Application'] = Application::NAME;
                return new Response(200, [], ['foo' => 'bar']);
            }
        };

        $errorMiddleware = new ErrorMiddleware(true);
        $response = $errorMiddleware->process($request, $handler);

        self::assertSame(500, $response->getStatusCode());
    }

    private function getRequest(): ServerRequestInterface
    {
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator(
            $psr17Factory, // ServerRequestFactory
            $psr17Factory, // UriFactory
            $psr17Factory, // UploadedFileFactory
            $psr17Factory  // StreamFactory
        );

        return $creator->fromGlobals();
    }
}
