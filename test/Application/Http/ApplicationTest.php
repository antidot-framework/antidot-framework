<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http;

use Antidot\Application\Http\Application;
use Antidot\Application\Http\Middleware\LazyLoadingMiddleware;
use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Application\Http\Route;
use Antidot\Application\Http\RouteFactory;
use Antidot\Application\Http\Router;
use Antidot\Container\MiddlewareFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

class ApplicationTest extends TestCase
{
    /** @var RequestHandlerRunner|MockObject */
    private $runner;
    /** @var MiddlewareFactory */
    private $middlewareFactory;
    /** @var Pipeline|MockObject */
    private $pipeline;
    /** @var Router|MockObject */
    private $router;
    /** @var Application */
    private $app;
    /** @var array */
    private $routeData;
    /** @var ContainerInterface|MockObject */
    private $container;
    /** @var RouteFactory|MockObject */
    private $routeFactory;

    public function testItShouldBeConstructedHavingNeededDependencies(): void
    {
        $this->givenRequestHandlerRunner();
        $this->givenMiddlewareFactory();
        $this->givenPipeline();
        $this->givenRouter();
        $this->givenARouteFactory();
        $this->whenApplicationConstructorIsCalled();
        $this->thenApplicationShouldBeCreated();
    }

    public function testItShouldAddPipesToGlobalMiddleware(): void
    {
        $this->havingAnApplication();
        $this->whenApplicationPipeIsCalled();
        $this->thenApplicationShouldBeCreated();
    }

    public function testItShouldAddPostRoutesToGlobalMiddleware(): void
    {
        $this->givenARouteData();
        $this->havingAnApplication();
        $this->whenApplicationPostIsCalled();
        $this->thenApplicationShouldBeCreated();
    }

    public function testItShouldAddGetRoutesToGlobalMiddleware(): void
    {
        $this->givenARouteData();
        $this->havingAnApplication();
        $this->whenApplicationGetIsCalled();
        $this->thenApplicationShouldBeCreated();
    }

    public function testItShouldAddPutRoutesToGlobalMiddleware(): void
    {
        $this->givenARouteData();
        $this->havingAnApplication();
        $this->whenApplicationPutIsCalled();
        $this->thenApplicationShouldBeCreated();
    }

    public function testItShouldAddPatchRoutesToGlobalMiddleware(): void
    {
        $this->givenARouteData();
        $this->havingAnApplication();
        $this->whenApplicationPatchIsCalled();
        $this->thenApplicationShouldBeCreated();
    }

    public function testItShouldAddDeleteRoutesToGlobalMiddleware(): void
    {
        $this->givenARouteData();
        $this->havingAnApplication();
        $this->whenApplicationDeleteIsCalled();
        $this->thenApplicationShouldBeCreated();
    }

    public function testItShouldAddOptionsRoutesToGlobalMiddleware(): void
    {
        $this->givenARouteData();
        $this->havingAnApplication();
        $this->whenApplicationOptionsIsCalled();
        $this->thenApplicationShouldBeCreated();
    }

    public function testItShouldRunConfiguredApplication(): void
    {
        $this->givenARouteData();
        $this->havingAConfiguredApplication();
        $this->whenApplicationRuns();
        $this->thenApplicationShouldBeCreated();
    }

    private function givenRequestHandlerRunner(): void
    {
        $this->runner = $this->createMock(RequestHandlerRunner::class);
    }

    private function givenMiddlewareFactory(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->middlewareFactory = new MiddlewareFactory($this->container);
    }

    private function givenPipeline(): void
    {
        $this->pipeline = $this->createMock(Pipeline::class);
    }

    private function givenRouter(): void
    {
        $this->router = $this->createMock(Router::class);
    }

    private function givenARouteFactory(): void
    {
        $this->routeFactory = $this->createMock(RouteFactory::class);
    }

    private function whenApplicationConstructorIsCalled(): void
    {
        $this->app = new Application(
            $this->runner,
            $this->pipeline,
            $this->router,
            $this->middlewareFactory,
            $this->routeFactory
        );
    }

    private function thenApplicationShouldBeCreated(): void
    {
        $this->assertInstanceOf(Application::class, $this->app);
    }

    private function havingAnApplication(): void
    {
        $this->givenRequestHandlerRunner();
        $this->givenMiddlewareFactory();
        $this->givenPipeline();
        $this->givenRouter();
        $this->givenARouteFactory();
        $this->whenApplicationConstructorIsCalled();
    }

    private function givenARouteData(): void
    {
        $this->routeData = ['/some-path', ['SomeMiddleware'], 'some.path'];
    }

    private function whenApplicationPipeIsCalled(): void
    {
        $this->pipeline
            ->expects($this->once())
            ->method('pipe')
            ->with($this->isInstanceOf(LazyLoadingMiddleware::class));
        $this->app->pipe('SomeMiddleware');
    }

    private function whenApplicationGetIsCalled(): void
    {
        $this->router
            ->expects($this->once())
            ->method('append')
            ->with($this->isInstanceOf(Route::class));
        $this->app->get($this->routeData[0], $this->routeData[1], $this->routeData[2]);
    }

    private function whenApplicationPostIsCalled(): void
    {
        $this->router
            ->expects($this->once())
            ->method('append')
            ->with($this->isInstanceOf(Route::class));
        $this->app->post($this->routeData[0], $this->routeData[1], $this->routeData[2]);
    }

    private function whenApplicationPutIsCalled(): void
    {
        $this->router
            ->expects($this->once())
            ->method('append')
            ->with($this->isInstanceOf(Route::class));
        $this->app->put($this->routeData[0], $this->routeData[1], $this->routeData[2]);
    }

    private function whenApplicationPatchIsCalled(): void
    {
        $this->router
            ->expects($this->once())
            ->method('append')
            ->with($this->isInstanceOf(Route::class));
        $this->app->patch($this->routeData[0], $this->routeData[1], $this->routeData[2]);
    }

    private function whenApplicationDeleteIsCalled(): void
    {
        $this->router
            ->expects($this->once())
            ->method('append')
            ->with($this->isInstanceOf(Route::class));
        $this->app->delete($this->routeData[0], $this->routeData[1], $this->routeData[2]);
    }

    private function whenApplicationOptionsIsCalled(): void
    {
        $this->router
            ->expects($this->once())
            ->method('append')
            ->with($this->isInstanceOf(Route::class));
        $this->app->options($this->routeData[0], $this->routeData[1], $this->routeData[2]);
    }

    private function havingAConfiguredApplication(): void
    {
        $this->havingAnApplication();
        $this->whenApplicationGetIsCalled();
    }

    private function whenApplicationRuns(): void
    {
        $this->app->run();
    }
}
