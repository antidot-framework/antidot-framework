<?php

declare(strict_types=1);

namespace AntidotTest\Container\Config;

use Antidot\Application\Http\Application;
use Antidot\Application\Http\Response\ErrorResponseGenerator;
use Antidot\Application\Http\RouteFactory;
use Antidot\Application\Http\Router;
use Antidot\Container\ApplicationFactory;
use Antidot\Container\MiddlewareFactory;
use Antidot\Container\RequestFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;

class ApplicationFactoryTest extends TestCase
{
    /** @var MockObject|ContainerInterface */
    private $container;
    /** @var Application */
    private $application;

    public function testItShouldCreateServerRequestFromServerGlobals(): void
    {
        $this->givenAPsrContainer();
        $this->whenFactoryIsInvoked();
        $this->thenItShouldReturnAnInstanceOfApplication();
    }

    private function givenAPsrContainer(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container
            ->expects($this->exactly(6))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                $this->createMock(EmitterStack::class),
                function () {},
                $this->createMock(ErrorResponseGenerator::class),
                $this->createMock(Router::class),
                $this->createMock(MiddlewareFactory::class),
                $this->createMock(RouteFactory::class)
            )
            ->withConsecutive(
                [EmitterStack::class],
                [RequestFactory::class],
                [ErrorResponseGenerator::class],
                [Router::class],
                [MiddlewareFactory::class],
                [RouteFactory::class]
            )
        ;
    }

    private function whenFactoryIsInvoked(): void
    {
        $factory = new ApplicationFactory();
        $this->application = $factory->__invoke($this->container);
    }

    private function thenItShouldReturnAnInstanceOfApplication(): void
    {
        $this->assertInstanceOf(Application::class, $this->application);
    }
}
