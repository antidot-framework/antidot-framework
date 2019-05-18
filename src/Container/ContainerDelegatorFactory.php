<?php

declare(strict_types=1);

namespace Antidot\Container;

use Aura\Di\Container;

/**
 * @deprecated will remove in version 1.0.0
 *
 * Aura.Di-compatible delegator factory.
 *
 * Map an instance of this:
 *
 * <code>
 * $container->set(
 *     $serviceName,
 *     $container->lazyGetCall(
 *         $delegatorFactoryInstance,
 *         'build',
 *         $container,
 *         $serviceName
 *     )
 * )
 * </code>
 *
 * Instances receive the list of delegator factory names or instances, and a
 * closure that can create the initial service instance to pass to the first
 * delegator.
 */
final class ContainerDelegatorFactory
{
    /**
     * @var array Either delegator factory names or instances.
     */
    private $delegators;

    /**
     * @var callable
     */
    private $factory;

    /**
     * @param array $delegators Array of delegator factory names or instances.
     * @param callable $factory Callable that can return the initial instance.
     */
    public function __construct(array $delegators, callable $factory)
    {
        $this->delegators = $delegators;
        $this->factory = $factory;
    }

    /**
     * Build the instance, invoking each delegator with the result of the previous.
     *
     * @param Container $container
     * @param string $serviceName
     * @return mixed
     */
    public function build(Container $container, $serviceName)
    {
        $factory = $this->factory;

        return \array_reduce(
            $this->delegators,
            static function ($instance, $delegatorName) use ($serviceName, $container) {
                $delegator = \is_callable($delegatorName) ? $delegatorName : new $delegatorName();

                return $delegator($container, $serviceName, static function () use ($instance) {
                    return $instance;
                });
            },
            $factory()
        );
    }
}
