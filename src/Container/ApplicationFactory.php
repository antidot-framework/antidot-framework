<?php

declare(strict_types=1);

namespace Antidot\Container;

use Antidot\Application\Http\Application;
use Antidot\Application\Http\Middleware\MiddlewarePipeline;
use Antidot\Application\Http\Middleware\Pipeline;
use Antidot\Application\Http\Response\ErrorResponseGenerator;
use Antidot\Application\Http\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use SplQueue;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

final class ApplicationFactory
{
    public function __invoke(ContainerInterface $container): Application
    {
        $pipeline = new MiddlewarePipeline(new SplQueue());
        $runner = $this->getRunner($container, $pipeline);
        return new Application(
            $runner,
            $pipeline,
            $container->get(Router::class),
            $container->get(MiddlewareFactory::class)
        );
    }

    private function getRunner(ContainerInterface $container, Pipeline $pipeline): RequestHandlerRunner
    {
        return new RequestHandlerRunner(
            $pipeline,
            $container->get(EmitterStack::class),
            static function (): RequestInterface {
                return ServerRequestFactory::fromGlobals(
                    $_SERVER,
                    $_GET,
                    $_POST,
                    $_COOKIE,
                    $_FILES
                );
            },
            $container->get(ErrorResponseGenerator::class)
        );
    }
}
