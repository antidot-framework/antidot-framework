<?php

declare(strict_types=1);

namespace AntidotTest\Container\Config;

use Antidot\Application\Http\Application;
use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Container\Config\ContainerConfig;
use Antidot\Container\EmitterFactory;
use ArrayObject;
use Aura\Di\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

class ContainerConfigTest extends TestCase
{
    /** @var Container|MockObject */
    private $container;
    /** @var array */
    private $config;

    private $result;
    /** @var array */
    private $arguments;

    public function testItCanNotModifyCOntainer(): void
    {
        $this->givenAnEmptyContainer();
        $this->havingAContainerWithInvokablesConfig();
        $this->whenContainerConfigIsModified();
        $this->thenContainerShouldDoNothing();
    }

    public function testItShouldDoNothingWhenConfigHasNotDependencies(): void
    {
        $this->givenAnEmptyContainer();
        $this->havingAnEmptyContainerConfig();
        $this->whenContainerConfigIsDefined();
        $this->thenContainerShouldDoNothing();
    }

    public function testItShouldBuildContainerWithGivenConfigInvokableServices(): void
    {
        $this->givenAnEmptyContainer();
        $this->havingAContainerWithInvokablesConfig();
        $this->thenContainerShouldHaveConfiguredInvokableDependencies();
        $this->whenContainerConfigIsDefined();
    }

    public function testItShouldBuildContainerWithGivenConfigAliasServices(): void
    {
        $this->givenAnEmptyContainer();
        $this->havingAContainerWithAliasesConfig();
        $this->thenContainerShouldHaveConfiguredAliasDependencies();
        $this->whenContainerConfigIsDefined();
    }

    public function testItShouldBuildContainerWithGivenConfigServices(): void
    {
        $this->givenAnEmptyContainer();
        $this->havingAContainerWithServicesConfig();
        $this->thenContainerShouldHaveConfiguredServicesDependencies();
        $this->whenContainerConfigIsDefined();
    }

    public function testItShouldBuildContainerWithGivenConfigFactoryServices(): void
    {
        $this->givenAnEmptyContainer();
        $this->havingAContainerWithFactoriesConfig();
        $this->thenContainerShouldHaveConfiguredFactoriesDependencies();
        $this->whenContainerConfigIsDefined();
    }

    public function testItShouldBuildContainerWithGivenConfigFactoryArrayServices(): void
    {
        $this->givenAnEmptyContainer();
        $this->havingAContainerWithFactoriesArrayConfig();
        $this->thenContainerShouldHaveConfiguredFactoriesArrayDependencies();
        $this->whenContainerConfigIsDefined();
    }

    public function testItShouldBuildContainerWithGivenConfigConditionalServices(): void
    {
        $this->givenAnEmptyContainer();
        $this->havingAContainerWithConditionalConfig();
        $this->thenContainerShouldHaveConfiguredConditionalDependencies();
        $this->whenContainerConfigIsDefined();
    }

    public function testItShouldBuildContainerWithGivenConfigConditionalNotExistingServices(): void
    {
        $this->givenAnEmptyContainer();
        $this->havingAContainerWithConditionalConfig();
        $this->thenContainerShouldHaveConfiguredConditionalNonExistingDependencies();
        $this->whenContainerConfigIsDefined();
    }

    public function testItShouldBuildContainerWithGivenConfigConditionalArrayParamsServices(): void
    {
        $this->givenAnEmptyContainer();
        $this->havingAContainerWithConditionalArrayParamsConfig();
        $this->thenContainerShouldHaveConfiguredConditionalArrayParamsDependencies();
        $this->whenContainerConfigIsDefined();
    }

    private function givenAnEmptyContainer(): void
    {
        $this->container = $this->createMock(Container::class);
    }

    private function havingAContainerWithInvokablesConfig(): void
    {
        $this->config = [
            'dependencies' => [
                'invokables' => [
                    Pipeline::class => Pipeline::class,
                ],
            ],
        ];
    }

    private function whenContainerConfigIsDefined(): void
    {
        $containerConfig = new ContainerConfig($this->config);

        $this->result = $containerConfig->define($this->container);
    }

    private function thenContainerShouldHaveConfiguredInvokableDependencies(): void
    {
        $pipeline = $this->createMock(Pipeline::class);
        $factory = static function () use ($pipeline) {
            return $pipeline;
        };

        $this->container
            ->expects($this->at(0))
            ->method('set')
            ->with('config', new ArrayObject($this->config, ArrayObject::ARRAY_AS_PROPS));
        $this->container
            ->expects($this->at(1))
            ->method('lazyNew')
            ->with(Pipeline::class)
            ->willReturn($factory);
        $this->container
            ->expects($this->at(2))
            ->method('set')
            ->with(Pipeline::class, $factory);
        $this->container
            ->expects($this->at(3))
            ->method('lazyGet')
            ->with(Pipeline::class);
    }

    private function havingAnEmptyContainerConfig(): void
    {
        $this->config = [];
    }

    private function thenContainerShouldDoNothing(): void
    {
        $this->assertNull($this->result);
    }

    private function whenContainerConfigIsModified(): void
    {
        $containerConfig = new ContainerConfig($this->config);

        $this->result = $containerConfig->modify($this->container);
    }

    private function havingAContainerWithAliasesConfig(): void
    {
        $this->config = [
            'dependencies' => [
                'aliases' => [
                    'pipeline' => Pipeline::class,
                ],
            ],
        ];
    }

    private function thenContainerShouldHaveConfiguredAliasDependencies(): void
    {
        $pipeline = $this->createMock(Pipeline::class);
        $factory = static function () use ($pipeline) {
            return $pipeline;
        };

        $this->container
            ->expects($this->at(0))
            ->method('set')
            ->with('config', new ArrayObject($this->config, ArrayObject::ARRAY_AS_PROPS));
        $this->container
            ->expects($this->at(1))
            ->method('lazyNew')
            ->with(Pipeline::class)
            ->willReturn($factory);
        $this->container
            ->expects($this->at(2))
            ->method('set')
            ->with('pipeline', $factory);
        $this->container
            ->expects($this->at(3))
            ->method('lazyGet')
            ->with('pipeline');
    }

    private function havingAContainerWithServicesConfig(): void
    {
        $this->config = [
            'dependencies' => [
                'services' => [
                    'pipeline' => Pipeline::class,
                ],
            ],
        ];
    }

    private function thenContainerShouldHaveConfiguredServicesDependencies(): void
    {
        $pipeline = $this->createMock(Pipeline::class);
        $factory = static function () use ($pipeline) {
            return $pipeline;
        };

        $this->container
            ->expects($this->at(0))
            ->method('set')
            ->with('config', new ArrayObject($this->config, ArrayObject::ARRAY_AS_PROPS));
        $this->container
            ->expects($this->at(1))
            ->method('lazyNew')
            ->with(Pipeline::class)
            ->willReturn($factory);
        $this->container
            ->expects($this->at(2))
            ->method('set')
            ->with('pipeline', $factory);
        $this->container
            ->expects($this->at(3))
            ->method('lazyGet')
            ->with('pipeline');
    }

    private function havingAContainerWithFactoriesConfig(): void
    {
        $this->config = [
            'dependencies' => [
                'factories' => [
                    EmitterInterface::class => EmitterFactory::class,
                ],
            ],
        ];
    }

    private function thenContainerShouldHaveConfiguredFactoriesDependencies(): void
    {
        $factory = $this->createMock(EmitterFactory::class);
        $factory
            ->method('__invoke')
            ->with($this->container)
            ->willReturn($this->createMock(EmitterInterface::class));
        $factoryFactory = function () use ($factory) {
            return $factory;
        };

        $this->container
            ->expects($this->at(0))
            ->method('set')
            ->with('config', new ArrayObject($this->config, ArrayObject::ARRAY_AS_PROPS));
        $this->container
            ->expects($this->at(1))
            ->method('lazyNew')
            ->with(EmitterFactory::class)
            ->willReturn($factoryFactory);
        $this->container
            ->expects($this->at(2))
            ->method('set')
            ->with(EmitterFactory::class, $factoryFactory);
        $this->container
            ->expects($this->at(3))
            ->method('lazyGetCall')
            ->with(EmitterFactory::class, '__invoke', $this->container)
            ->willReturn($factory);
        $this->container
            ->expects($this->at(4))
            ->method('set')
            ->with(EmitterInterface::class, $factory);
        $this->container
            ->expects($this->at(5))
            ->method('lazyGet')
            ->with(EmitterInterface::class);
    }

    private function havingAContainerWithFactoriesArrayConfig(): void
    {
        $this->config = [
            'dependencies' => [
                'factories' => [
                    EmitterInterface::class => [EmitterFactory::class, 'default'],
                ],
            ],
        ];
    }

    private function thenContainerShouldHaveConfiguredFactoriesArrayDependencies(): void
    {
        $factory = $this->createMock(EmitterFactory::class);
        $factory
            ->method('__invoke')
            ->with($this->container, 'default')
            ->willReturn($this->createMock(EmitterInterface::class));
        $factoryFactory = function () use ($factory) {
            return $factory;
        };

        $this->container
            ->expects($this->at(0))
            ->method('set')
            ->with('config', new ArrayObject($this->config, ArrayObject::ARRAY_AS_PROPS));
        $this->container
            ->expects($this->at(1))
            ->method('lazyNew')
            ->with(EmitterFactory::class)
            ->willReturn($factoryFactory);
        $this->container
            ->expects($this->at(2))
            ->method('set')
            ->with(EmitterFactory::class, $factoryFactory);
        $this->container
            ->expects($this->at(3))
            ->method('lazyGetCall')
            ->with(EmitterFactory::class, '__invoke', $this->container, 'default')
            ->willReturn($factory);
        $this->container
            ->expects($this->at(4))
            ->method('set')
            ->with(EmitterInterface::class, $factory);
        $this->container
            ->expects($this->at(5))
            ->method('lazyGet')
            ->with(EmitterInterface::class);
    }

    private function havingAContainerWithConditionalConfig(): void
    {
        $this->arguments = [
            'runner' => RequestHandlerRunner::class,
        ];
        $this->config = [
            'dependencies' => [
                'conditionals' => [
                    Application::class => [
                        'class' => Application::class,
                        'arguments' => $this->arguments,
                    ],
                ],
            ],
        ];
    }

    private function thenContainerShouldHaveConfiguredConditionalDependencies(): void
    {
        $this->container
            ->expects($this->at(0))
            ->method('set')
            ->with('config', new ArrayObject($this->config, ArrayObject::ARRAY_AS_PROPS));
        $this->container
            ->expects($this->exactly(2))
            ->method('has')
            ->with($this->logicalOr(RequestHandlerRunner::class, Application::class))
            ->willReturn(true);
        $this->container
            ->expects($this->at(2))
            ->method('lazyGet')
            ->with(RequestHandlerRunner::class)
            ->willReturn($this->createMock(RequestHandlerRunner::class));
    }

    private function thenContainerShouldHaveConfiguredConditionalNonExistingDependencies(): void
    {
        $this->container
            ->expects($this->at(0))
            ->method('set')
            ->with('config', new ArrayObject($this->config, ArrayObject::ARRAY_AS_PROPS));
        $this->container
            ->expects($this->exactly(2))
            ->method('has')
            ->with($this->logicalOr(RequestHandlerRunner::class, Application::class))
            ->willReturn(false);
        $this->container
            ->expects($this->exactly(2))
            ->method('lazyNew')
            ->with($this->logicalOr(RequestHandlerRunner::class, Application::class));
        $this->container
            ->expects($this->exactly(3))
            ->method('lazyGet')
            ->with($this->logicalOr(RequestHandlerRunner::class, Application::class));
    }

    private function havingAContainerWithConditionalArrayParamsConfig(): void
    {
        $this->arguments = [
            'config' => [1, 2, 3],
        ];
        $this->config = [
            'dependencies' => [
                'conditionals' => [
                    Application::class => [
                        'class' => Application::class,
                        'arguments' => $this->arguments,
                    ],
                ],
            ],
        ];
    }

    private function thenContainerShouldHaveConfiguredConditionalArrayParamsDependencies(): void
    {
        $application = $this->createMock(Application::class);
        $this->container
            ->expects($this->exactly(2))
            ->method('set')
            ->with(
                $this->logicalOr('config', Application::class),
                $this->logicalOr(new ArrayObject($this->config, ArrayObject::ARRAY_AS_PROPS), $application)
            );
        $this->container
            ->expects($this->exactly(1))
            ->method('lazyNew')
            ->with(Application::class, $this->arguments)
            ->willReturn($application);
        $this->container
            ->expects($this->exactly(1))
            ->method('lazyGet')
            ->with(Application::class);
        $this->container
            ->expects($this->exactly(1))
            ->method('has')
            ->with(Application::class)
            ->willReturn(true);

    }
}
