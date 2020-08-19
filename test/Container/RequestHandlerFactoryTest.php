<?php

declare(strict_types=1);

namespace AntidotTest\Container;

use Antidot\Application\Http\Handler\CallableRequestHandler;
use Antidot\Application\Http\Handler\LazyLoadingRequestHandler;
use Antidot\Container\RequestHandlerFactory;
use InvalidArgumentException;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplObjectStorage;

use function get_class;
use function sprintf;

class RequestHandlerFactoryTest extends TestCase
{
    /** @var MockObject|ContainerInterface */
    private $container;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    public function testItShouldReturnAlreadyInstantiatedARequestHandler(): void
    {
        $expectedHandler = $this->createMock(RequestHandlerInterface::class);
        $factory = new RequestHandlerFactory($this->container);

        $handler = $factory->create($expectedHandler);

        $this->assertSame($expectedHandler, $handler);
    }

    public function testItShouldThrowAnExceptionWithInvalidRequestHandler(): void
    {
        $invalidHandler = $this->createMock(SplObjectStorage::class);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            RequestHandlerFactory::INVALID_HANDLER_MESSAGE,
            get_class($invalidHandler)
        ));
        $factory = new RequestHandlerFactory($this->container);
        $factory->create($invalidHandler);
    }

    public function testItShouldReturnInstantiatedARequestHandlerFromContainer(): void
    {
        $handlerName = RequestHandlerInterface::class;
        $factory = new RequestHandlerFactory($this->container);

        $handler = $factory->create($handlerName);

        $this->assertInstanceOf(LazyLoadingRequestHandler::class, $handler);
    }

    public function testItShouldReturnInstantiatedARequestHandlerFromAnonymousFunction(): void
    {
        $handlerName = static function (): ResponseInterface {
            return new Response('');
        };
        $factory = new RequestHandlerFactory($this->container);

        $handler = $factory->create($handlerName);

        $this->assertInstanceOf(CallableRequestHandler::class, $handler);
    }
}
