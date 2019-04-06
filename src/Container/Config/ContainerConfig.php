<?php

declare(strict_types=1);

namespace Antidot\Container\Config;

use Antidot\Container\ContainerDelegatorFactory;
use ArrayObject;
use Aura\Di\Container;
use Aura\Di\ContainerConfigInterface;

/**
 * Configuration for the Aura.Di container.
 *
 * This class provides functionality for the following service types:
 *
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
            $dependencies = $this->marshalDelegators($container, $dependencies);
        }
        if (isset($dependencies['services'])) {
            foreach ($dependencies['services'] as $name => $service) {
                $container->set($name, $service);
                $container->types[$name] = $container->lazyGet($name);
            }
        }
        if (isset($dependencies['factories'])) {
            foreach ($dependencies['factories'] as $service => $factory) {
                if (is_array($factory)) {
                    $container->set($factory[0], $container->lazyNew($factory[0]));
                    $container->set($service, $container->lazyGetCall(
                        $factory[0],
                        '__invoke',
                        $container,
                        $factory[1]
                    ));
                } else {
                    $container->set($factory, $container->lazyNew($factory));
                    $container->set($service, $container->lazyGetCall($factory, '__invoke', $container));
                }
                $container->types[$service] = $container->lazyGet($service);
            }
        }
        if (isset($dependencies['conditionals'])) {
            foreach ($dependencies['conditionals'] as $id => $conditional) {
                $params = [];
                foreach ($conditional['arguments'] as $type => $implementation) {
                    if (\is_array($implementation)) {
                        $params[$type] = $implementation;
                        $container->params[$id][$type] = $implementation;
                        $container->set($id, $container->lazyNew($conditional['class'], $params));
                        $container->types[$id] = $container->lazyGet($id);
                        continue;
                    }

                    if (!$container->has($implementation)) {
                        $container->set($implementation, $container->lazyNew($implementation));
                        $container->types[$implementation] = $container->lazyGet($implementation);
                    }
                    $params[$type] = $container->lazyGet($implementation);
                    $container->params[$id][$type] = $container->lazyGet($implementation);
                }
                if (!$container->has($id)) {
                    $container->set($id, $container->lazyNew($conditional['class'], $params));
                    $container->types[$id] = $container->lazyGet($id);
                }
            }
        }
        if (isset($dependencies['invokables'])) {
            foreach ($dependencies['invokables'] as $service => $class) {
                $container->set($service, $container->lazyNew($class));
                $container->types[$service] = $container->lazyGet($service);
            }
        }
        if (isset($dependencies['aliases'])) {
            foreach ($dependencies['aliases'] as $alias => $target) {
                $container->set($alias, $container->lazyGet($target));
                $container->types[$alias] = $container->lazyGet($target);
            }
        }
    }

    public function modify(Container $container)
    {
    }

    private function marshalDelegators(Container $container, array $dependencies): array
    {
        foreach ($dependencies['delegators'] as $service => $delegatorNames) {
            $factory = null;
            if (isset($dependencies['services'][$service])) {
                // Marshal from service
                $instance = $dependencies['services'][$service];
                $factory = function () use ($instance) {
                    return $instance;
                };
                unset($dependencies['service'][$service]);
            }
            if (isset($dependencies['factories'][$service])) {
                // Marshal from factory
                $serviceFactory = $dependencies['factories'][$service];
                $factory = function () use ($service, $serviceFactory, $container) {
                    $aService = new $serviceFactory();

                    return \is_callable($serviceFactory)
                        ? $serviceFactory($container, $service)
                        : $aService($container, $service);
                };
                unset($dependencies['factories'][$service]);
            }
            if (isset($dependencies['invokables'][$service])) {
                // Marshal from invokable
                $class = $dependencies['invokables'][$service];
                $factory = function () use ($class) {
                    return new $class();
                };
                unset($dependencies['invokables'][$service]);
            }
            if (!\is_callable($factory)) {
                continue;
            }
            $delegatorFactory = 'AuraDelegatorFactory::'.$service;
            $container->set($delegatorFactory, static function () use ($delegatorNames, $factory) {
                return new ContainerDelegatorFactory($delegatorNames, $factory);
            });
            $container->set(
                $service,
                $container->lazyGetCall($delegatorFactory, 'build', $container, $service)
            );
            $container->types[$service] = $container->lazyGet($service);
        }

        return $dependencies;
    }
}
