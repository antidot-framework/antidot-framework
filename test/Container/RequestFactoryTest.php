<?php

declare(strict_types=1);

namespace AntidotTest\Container\Config;

use Antidot\Container\RequestFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;

class RequestFactoryTest extends TestCase
{
    /** @var MockObject|ContainerInterface */
    private $container;
    /** @var callable */
    private $request;

    public function testItShouldFilterNonStringValuesFromServerGlobalsWhenCreatingRequest(): void
    {
        $this->havingAnArrayValueInServerGlobals();
        $this->givenAPsrContainer();
        $this->whenFactoryIsInvoked();
        $this->thenItShouldReturnACallableRequest();
    }

    private function givenAPsrContainer(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }
    private function havingAnArrayValueInServerGlobals(): void
    {
        $_SERVER['argc'] = [];
    }

    private function whenFactoryIsInvoked(): void
    {
        $factory = new RequestFactory();
        $this->request = $factory->__invoke();
    }

    private function thenItShouldReturnACallableRequest(): void
    {
        $request = $this->request;
        $this->assertInstanceOf(RequestInterface::class, $request());
    }
}
