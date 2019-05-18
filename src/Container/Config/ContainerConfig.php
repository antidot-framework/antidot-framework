<?php

declare(strict_types=1);

namespace Antidot\Container\Config;

use ArrayObject;
use Aura\Di\Container;
use Aura\Di\ContainerConfigInterface;

use function is_array;

/**
 * @deprecated will remove in version 1.0.0
 *
 * Configuration for the Aura.Di container.
 * This class provides functionality for the following service types:
 * - Aliases
 * - Delegators
 * - Factories
 * - Invokable classes
 * - Services (known instances)
 * - conditionals
 */
final class ContainerConfig implements ContainerConfigInterface
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function define(Container $container)
    {
        $container->set('config', new ArrayObject($this->config, ArrayObject::ARRAY_AS_PROPS));
        if (empty($this->config['dependencies'])) {
            return null;
        }
        $dependencies = $this->config['dependencies'];
        if (isset($dependencies['delegators'])) {
            $dependencies = (new MarshalDelegatorsConfig())($container, $dependencies);
        }
        $this->lazyLoadServices($container, $dependencies);
        $this->lazyLoadFactories($container, $dependencies);
        $this->lazyLoadConditionals($container, $dependencies);
        $this->lazyLoadInvokables($container, $dependencies);
        $this->lazyLoadAliases($container, $dependencies);
    }

    public function modify(Container $container)
    {
    }

    private function lazyLoad(Container $container, array $dependencies, string $type): void
    {
        foreach ($dependencies[$type] as $service => $class) {
            $container->set($service, 'aliases' === $type ? $container->lazyGet($class) : $container->lazyNew($class));
            $container->types[$service] = $container->lazyGet($service);
        }
    }

    private function lazyLoadFactories(Container $container, array $dependencies): void
    {
        if (empty($dependencies['factories'])) {
            return;
        }
        foreach ($dependencies['factories'] as $service => $factory) {
            if (is_array($factory)) {
                $container->set($factory[0], $container->lazyNew($factory[0]));
                $container->set($service, $container->lazyGetCall(
                    $factory[0],
                    '__invoke',
                    $container,
                    $factory[1] ?? null
                ));
            } else {
                $container->set($factory, $container->lazyNew($factory));
                $container->set($service, $container->lazyGetCall($factory, '__invoke', $container));
            }
            $container->types[$service] = $container->lazyGet($service);
        }
    }

    private function lazyLoadConditionals(Container $container, array $dependencies): void
    {
        if (empty($dependencies['conditionals'])) {
            return;
        }

        foreach ($dependencies['conditionals'] as $id => $conditional) {
            $params = $this->setConditionalArguments($container, $id, $conditional);
            if (!$container->has($id)) {
                $container->set($id, $container->lazyNew($conditional['class'], $params));
                $container->types[$id] = $container->lazyGet($id);
            }
        }
    }

    private function lazyLoadInvokables(Container $container, array $dependencies): void
    {
        if (empty($dependencies['invokables'])) {
            return;
        }

        $this->lazyLoad($container, $dependencies, 'invokables');
    }

    private function lazyLoadAliases(Container $container, array $dependencies): void
    {
        if (empty($dependencies['aliases'])) {
            return;
        }

        $this->lazyLoad($container, $dependencies, 'aliases');
    }

    private function lazyLoadServices(Container $container, array $dependencies): void
    {
        if (empty($dependencies['services'])) {
            return;
        }

        $this->lazyLoad($container, $dependencies, 'services');
    }

    private function setConditionalArguments(Container $container, string $id, array $conditional): array
    {
        $params = [];
        foreach ($conditional['arguments'] as $type => $implementation) {
            if (is_array($implementation)) {
                $params[$type] = $implementation;
                $container->params[$id][$type] = $params[$type];
                $container->set($id, $container->lazyNew($conditional['class'], $params));
                $container->types[$id] = $container->lazyGet($id);
                continue;
            }

            if (!$container->has($implementation)) {
                $container->set($implementation, $container->lazyNew($implementation));
                $container->types[$implementation] = $container->lazyGet($implementation);
            }
            $params[$type] = $container->lazyGet($implementation);
            $container->params[$id][$type] = $params[$type];
        }

        return $params;
    }
}
