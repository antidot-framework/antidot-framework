<?php

declare(strict_types=1);

namespace Antidot\Test\Framework\Router;

use Antidot\Framework\Middleware\CallableRequestHandler;
use Antidot\Framework\Middleware\MethodNotAllowedMiddleware;
use Antidot\Framework\Middleware\MiddlewareFactory;
use Antidot\Framework\Middleware\PipedRouteMiddleware;
use Antidot\Framework\Middleware\RequestHandlerFactory;
use Antidot\Framework\Middleware\RouteNotFoundMiddleware;
use Antidot\Framework\Router\FastRouter;
use Antidot\Framework\Router\Route;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Uri;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class FastRouterTest extends TestCase
{
    public function testItShouldNotFoundRequestUriAndReturnAMiddleware(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $router = new FastRouter(
            new MiddlewareFactory($container),
            new RequestHandlerFactory($container)
        );

        $middleware = $router->match($this->getRequest());

        self::assertInstanceOf(RouteNotFoundMiddleware::class, $middleware);
    }

    public function testItShouldNotAllowRequestMethodAndReturnAMiddleware(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $router = new FastRouter(
            new MiddlewareFactory($container),
            new RequestHandlerFactory($container)
        );
        $router->append(new Route(['GET'], 'test', '/', []));
        $request = $this->getRequest();
        $request = $request->withUri(new Uri('/'));
        $request = $request->withMethod('POST');

        $middleware = $router->match($request);

        self::assertInstanceOf(MethodNotAllowedMiddleware::class, $middleware);
    }

    public function testItShouldMatchExistingRouteMiddleware(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->atLeastOnce())
            ->method('has')
            ->withConsecutive(
                ['some_middleware'],
                ['other_middleware'],
            )
            ->willReturnOnConsecutiveCalls(true, true);
        $router = new FastRouter(
            new MiddlewareFactory($container),
            new RequestHandlerFactory($container)
        );
        $router->append(new Route(['GET'], 'test', '/', ['some_middleware', 'other_middleware', 'some_handler']));
        $request = $this->getRequest();
        $request = $request->withUri(new Uri('/'));
        $request = $request->withMethod('GET');

        $middleware = $router->match($request);
        self::assertInstanceOf(PipedRouteMiddleware::class, $middleware);
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
