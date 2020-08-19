<?php

declare(strict_types=1);

namespace AntidotTest\Container;

use Antidot\Application\Http\Middleware\CallableMiddleware;
use Antidot\Application\Http\Middleware\LazyLoadingMiddleware;
use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use Antidot\Container\MiddlewareFactory;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use SplObjectStorage;

use function get_class;
use function sprintf;

class MiddlewareFactoryTest extends TestCase
{
    /** @var ContainerInterface|MockObject */
    private $container;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    public function testItShouldCreateMiddlewareWithMiddlewareName(): void
    {
        $middlewareName = 'SomeMiddleware';
        $factory = new MiddlewareFactory($this->container);

        $middleware = $factory->create($middlewareName);

        $this->assertInstanceOf(LazyLoadingMiddleware::class, $middleware);
    }

    public function testItShouldThrowAnExceptionWithInvalidMiddlewareNameType(): void
    {
        $invalidMiddlewareName = $this->createMock(SplObjectStorage::class);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            MiddlewareFactory::INVALID_MIDDLEWARE_MESSAGE,
            get_class($invalidMiddlewareName)
        ));
        $factory = new MiddlewareFactory($this->container);

        $factory->create($invalidMiddlewareName);

    }

    public function testItShouldCreateMiddlewareWithArrayOfMiddlewareNames(): void
    {
        $middlewareName = ['SomeMiddleware'];
        $factory = new MiddlewareFactory($this->container);

        $middleware = $factory->create($middlewareName);

        $this->assertInstanceOf(MiddlewarePipeline::class, $middleware);
    }

    public function testItShouldCreateMiddlewareWithAnonymousFunction(): void
    {
        $middlewareName = static function(): ResponseInterface {};
        $factory = new MiddlewareFactory($this->container);

        $middleware = $factory->create($middlewareName);

        $this->assertInstanceOf(CallableMiddleware::class, $middleware);
    }
}
