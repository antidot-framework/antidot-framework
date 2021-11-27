<?php

declare(strict_types=1);

namespace Antidot\Test\Framework;

use Antidot\Framework\Application;
use Antidot\Framework\Middleware\MiddlewareFactory;
use Antidot\Framework\Middleware\RequestHandlerFactory;
use Antidot\Framework\Middleware\RouteDispatcherMiddleware;
use Antidot\Framework\Router\FastRouter;
use Antidot\Framework\Router\RouteFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Uri;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApplicationTest extends TestCase
{
    /** @var mixed|\PHPUnit\Framework\MockObject\MockObject|ContainerInterface */
    private mixed $container;
    private MiddlewareFactory $middlewareFactory;
    private FastRouter $router;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->middlewareFactory = new MiddlewareFactory($this->container);
        $this->router = new FastRouter(
            $this->middlewareFactory,
            new RequestHandlerFactory($this->container),
        );
    }

    public function testItShouldHandleHttpRequest(): void
    {
        $this->container
            ->method('get')
            ->with('some_middleware')
            ->willReturn($this->createMock(MiddlewareInterface::class));
        $request = $this->createMock(ServerRequestInterface::class);

        $application = $this->createApplication();
        $application->pipe('some_middleware');

        self::assertInstanceOf(ResponseInterface::class, $application->handle($request));
    }

    public function testItShouldHandlePostRequest(): void
    {
        $this->container
            ->method('get')
            ->with(RouteDispatcherMiddleware::class)
            ->willReturn(new RouteDispatcherMiddleware($this->router));
        $request = $this->getRequest();
        $request = $request->withMethod('POST');
        $request = $request->withUri(new Uri('/'));
        $request = $request->withAttribute('test', 'test_response');

        $application = $this->createApplication();
        $application->pipe(RouteDispatcherMiddleware::class);
        $application->post(
            '/',
            function(ServerRequestInterface $request): ResponseInterface {
                return new Response(201, [], $request->getAttribute('test'));
            },
            'test'
        );

        $response = $application->handle($request);

        self::assertSame(201, $response->getStatusCode());
        self::assertSame('test_response', (string)$response->getBody());
    }

    public function testItShouldHandleRequestAndReturnRouteNotFoundResponse(): void
    {
        $this->container
            ->method('get')
            ->with(RouteDispatcherMiddleware::class)
            ->willReturn(new RouteDispatcherMiddleware($this->router));
        $request = $this->getRequest();
        $request = $request->withMethod('POST');
        $request = $request->withUri(new Uri('/'));
        $request = $request->withAttribute('test', 'test_response');

        $application = $this->createApplication();
        $application->pipe(RouteDispatcherMiddleware::class);

        $response = $application->handle($request);

        self::assertSame(404, $response->getStatusCode());
        self::assertSame(['text/html'], $response->getHeader('Content-Type'));
        self::assertSame('<html><head></head><body>Page not found</body></html>', (string)$response->getBody());
    }

    public function testItShouldHandleRequestAndReturnMethodNotAllowedResponse(): void
    {
        $this->container
            ->method('get')
            ->with(RouteDispatcherMiddleware::class)
            ->willReturn(new RouteDispatcherMiddleware($this->router));
        $request = $this->getRequest();
        $request = $request->withMethod('POST');
        $request = $request->withUri(new Uri('/'));
        $request = $request->withAttribute('test', 'test_response');

        $application = $this->createApplication();
        $application->pipe(RouteDispatcherMiddleware::class);
        $application->get(
            '/',
                RequestHandlerInterface::class,
            'test'
        );

        $response = $application->handle($request);

        self::assertSame(403, $response->getStatusCode());
        self::assertSame(['text/html'], $response->getHeader('Content-Type'));
        self::assertSame('<html><head></head><body>Method Not Allowed</body></html>', (string)$response->getBody());
    }

    public function testItShouldHandleGetRequest(): void
    {
        $this->container
            ->method('get')
            ->with(RouteDispatcherMiddleware::class)
            ->willReturn(new RouteDispatcherMiddleware($this->router));
        $request = $this->getRequest();
        $request = $request->withMethod('GET');
        $request = $request->withUri(new Uri('/'));
        $request = $request->withAttribute('test', 'test_response');

        $application = $this->createApplication();
        $application->pipe(RouteDispatcherMiddleware::class);
        $application->get(
            '/',
            function(ServerRequestInterface $request): ResponseInterface {
                return new Response(200, [], $request->getAttribute('test'));
            },
            'test'
        );

        $response = $application->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('test_response', (string)$response->getBody());
    }

    public function testItShouldHandlePatchRequest(): void
    {
        $this->container
            ->method('get')
            ->with(RouteDispatcherMiddleware::class)
            ->willReturn(new RouteDispatcherMiddleware($this->router));
        $request = $this->getRequest();
        $request = $request->withMethod('PATCH');
        $request = $request->withUri(new Uri('/'));
        $request = $request->withAttribute('test', 'test_response');

        $application = $this->createApplication();
        $application->pipe(RouteDispatcherMiddleware::class);
        $application->patch(
            '/',
            function(ServerRequestInterface $request): ResponseInterface {
                return new Response(202, [], $request->getAttribute('test'));
            },
            'test'
        );

        $response = $application->handle($request);

        self::assertSame(202, $response->getStatusCode());
        self::assertSame('test_response', (string)$response->getBody());
    }

    public function testItShouldHandlePutRequest(): void
    {
        $this->container
            ->method('get')
            ->with(RouteDispatcherMiddleware::class)
            ->willReturn(new RouteDispatcherMiddleware($this->router));
        $request = $this->getRequest();
        $request = $request->withMethod('PUT');
        $request = $request->withUri(new Uri('/'));
        $request = $request->withAttribute('test', 'test_response');

        $application = $this->createApplication();
        $application->pipe(RouteDispatcherMiddleware::class);
        $application->put(
            '/',
            function(ServerRequestInterface $request): ResponseInterface {
                return new Response(204, [], $request->getAttribute('test'));
            },
            'test'
        );

        $response = $application->handle($request);

        self::assertSame(204, $response->getStatusCode());
        self::assertSame('test_response', (string)$response->getBody());
    }

    public function testItShouldHandleDeleteRequest(): void
    {
        $this->container
            ->method('get')
            ->with(RouteDispatcherMiddleware::class)
            ->willReturn(new RouteDispatcherMiddleware($this->router));
        $request = $this->getRequest();
        $request = $request->withMethod('DELETE');
        $request = $request->withUri(new Uri('/'));
        $request = $request->withAttribute('test', 'test_response');

        $application = $this->createApplication();
        $application->pipe(RouteDispatcherMiddleware::class);
        $application->delete(
            '/',
            [
                [
                    new class implements MiddlewareInterface {
                        public function process(
                            ServerRequestInterface $request,
                            RequestHandlerInterface $handler
                        ): ResponseInterface {
                            $request = $request->withAttribute('middleware_1', '1');
                            return $handler->handle($request);
                        }
                    },
                    function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
                        $request = $request->withAttribute('middleware_2', '2');
                        return $handler->handle($request);
                    }
                ],
                function (ServerRequestInterface $request): ResponseInterface {
                    return new Response(
                        204,
                        [],
                        $request->getAttribute('test')
                          . $request->getAttribute('middleware_1')
                          . $request->getAttribute('middleware_2')
                    );
                }
            ],
            'test'
        );

        $response = $application->handle($request);

        self::assertSame(204, $response->getStatusCode());
        self::assertSame('test_response12', (string)$response->getBody());
    }

    public function testItShouldHandleOptionsRequest(): void
    {
        $this->container
            ->method('get')
            ->with(RouteDispatcherMiddleware::class)
            ->willReturn(new RouteDispatcherMiddleware($this->router));
        $request = $this->getRequest();
        $request = $request->withMethod('OPTIONS');
        $request = $request->withUri(new Uri('/hello/koldo'));
        $request = $request->withAttribute('test', 'test_response');

        $application = $this->createApplication();
        $application->pipe(RouteDispatcherMiddleware::class);
        $application->options(
            '/hello/{name}',
            function(ServerRequestInterface $request): ResponseInterface {
                return new Response(200, [], $request->getAttribute('name'));
            },
            'test'
        );

        $response = $application->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('koldo', (string)$response->getBody());
    }

    public function testItShouldHandleRoutedRequest(): void
    {
        $this->container
            ->method('get')
            ->with(RouteDispatcherMiddleware::class)
            ->willReturn(new RouteDispatcherMiddleware($this->router));
        $request = $this->getRequest();
        $request = $request->withMethod('OPTIONS');
        $request = $request->withUri(new Uri('/hello/koldo'));
        $request = $request->withAttribute('test', 'test_response');

        $application = $this->createApplication();
        $application->pipe(RouteDispatcherMiddleware::class);
        $application->route(
            '/hello/{name}',
            function(ServerRequestInterface $request): ResponseInterface {
                return new Response(200, [], $request->getAttribute('name'));
            },
            ['OPTIONS'],
            'test'
        );

        $response = $application->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('koldo', (string)$response->getBody());
    }

    private function createApplication(): Application
    {
        return new Application(
            $this->middlewareFactory,
            new RouteFactory(),
            $this->router
        );
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
    