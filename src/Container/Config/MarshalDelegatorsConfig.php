<?php

declare(strict_types=1);

namespace Antidot\Container\Config;

use Antidot\Container\ContainerDelegatorFactory;
use Aura\Di\Container;

class MarshalDelegatorsConfig
{
    public function __invoke(Container $container, array $dependencies): array
    {
        foreach ($dependencies['delegators'] as $service => $delegatorNames) {
            $factory = null;
            $this->delegateServices($dependencies, $service, $factory);
            $this->delegateFactories($container, $dependencies, $service, $factory);
            $this->delegateInvokables($dependencies, $service, $factory);
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

    private function delegateServices(array $dependencies, string $service, ?callable &$factory): void
    {
        if (empty($dependencies['services'][$service])) {
            return;
        }
        // Marshal from service
        $instance = $dependencies['services'][$service];
        $factory = static function () use ($instance) {
            return $instance;
        };
        unset($dependencies['service'][$service]);
    }

    private function delegateFactories(
        Container $container,
        array $dependencies,
        string $service,
        ?callable &$factory
    ): void {
        if (empty($dependencies['factories'][$service])) {
            return;
        }
        // Marshal from factory
        $serviceFactory = $dependencies['factories'][$service];
        $factory = static function () use ($service, $serviceFactory, $container) {
            $aService = new $serviceFactory();

            return \is_callable($serviceFactory)
                ? $serviceFactory($container, $service)
                : $aService($container, $service);
        };
        unset($dependencies['factories'][$service]);
    }

    private function delegateInvokables(array $dependencies, string $service, ?callable &$factory): void
    {
        if (empty($dependencies['invokables'][$service])) {
            return;
        }
        // Marshal from invokable
        $class = $dependencies['invokables'][$service];
        $factory = function () use ($class) {
            return new $class();
        };
        unset($dependencies['invokables'][$service]);
    }
}
