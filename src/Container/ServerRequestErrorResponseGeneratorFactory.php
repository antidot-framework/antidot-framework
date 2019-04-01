<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Response\ErrorResponseGenerator;
use Antidot\Application\Http\Response\ServerRequestErrorResponseGenerator;
use Psr\Container\ContainerInterface;
use Zend\Diactoros\Response;

final class ServerRequestErrorResponseGeneratorFactory
{
    public function __invoke(ContainerInterface $container): ErrorResponseGenerator
    {
        return new ServerRequestErrorResponseGenerator(
            static function () {
                return new Response();
            }
        );
    }
}
