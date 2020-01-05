<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Container\ContainerInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

class EmitterFactory
{
    public function __invoke(ContainerInterface $container): EmitterInterface
    {
        $stack = new EmitterStack();
        $stack->push(new SapiEmitter());

        return $stack;
    }
}
