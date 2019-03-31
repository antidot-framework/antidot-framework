<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\ServerRequestErrorResponseGenerator;
use Psr\Container\ContainerInterface;
use Zend\Diactoros\Response;

final class ServerRequestErrorResponseGeneratorFactory
{
    public function __invoke(ContainerInterface $container): ServerRequestErrorResponseGenerator
    {
        return new ServerRequestErrorResponseGenerator(
            static function () {
                return new Response();
            }
        );
    }
}
