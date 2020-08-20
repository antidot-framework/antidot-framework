<?php

declare(strict_types=1);


namespace AntidotTest\Container;

use Antidot\Application\Http\Middleware\ErrorMiddleware;
use Antidot\Container\ErrorMiddlewareFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ErrorMiddlewareFactoryTest extends TestCase
{
    /** @var MockObject|ContainerInterface */
    private $container;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    /** @dataProvider getDebugConfig */
    public function testItShouldGenerateErrorMiddleware(array $config): void
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new ErrorMiddlewareFactory();
        $middleware = $factory->__invoke($this->container);

        $this->assertInstanceOf(ErrorMiddleware::class, $middleware);
    }

    public function getDebugConfig(): array
    {
        return [
            [
                ['debug' => true],
            ],
            [
                ['debug' => false],
            ],
            [
                []
            ]
        ];
    }
}
