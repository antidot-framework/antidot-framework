<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Container\ContainerInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

class EmitterFactory
{
    public function __invoke(ContainerInterface $container): EmitterInterface
    {
        $stack = new EmitterStack();
        $stack->push(new SapiEmitter());

        return $stack;
    }
}
