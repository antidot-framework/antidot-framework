<?php

declare(strict_types=1);

namespace Antidot\Container\Config;

use Antidot\Application\Cli\Command\ShowContainer;
use Antidot\Application\Http\Application;
use Antidot\Application\Http\Middleware\RouteDispatcherMiddleware;
use Antidot\Application\Http\Middleware\RouteNotFoundMiddleware;
use Antidot\Application\Http\Response\ErrorResponseGenerator;
use Antidot\Application\Http\Router;
use Antidot\Container\ApplicationFactory;
use Antidot\Container\EmitterFactory;
use Antidot\Container\MiddlewareFactory;
use Antidot\Container\MiddlewareFactoryFactory;
use Antidot\Container\ResponseFactory;
use Antidot\Container\ServerRequestErrorResponseGeneratorFactory;
use Antidot\Container\ShowContainerCommandFactory;
use Antidot\Container\StreamFactory;
use Antidot\Infrastructure\Aura\Router\AuraRouter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'console' => [
                'commands' => [
                    ShowContainer::NAME => ShowContainer::class,
                ],
                'helper-sets' => [],
                'dependencies' => [
                    'factories' => [
                        ShowContainer::class => ShowContainerCommandFactory::class,
                    ],
                ],
            ],
            'dependencies' => [
                'invokables' => [
                    RouteDispatcherMiddleware::class => RouteDispatcherMiddleware::class,
                    RouteNotFoundMiddleware::class => RouteNotFoundMiddleware::class,
                    Router::class => AuraRouter::class,
                ],
                'factories' => [
                    Application::class => ApplicationFactory::class,
                    MiddlewareFactory::class => MiddlewareFactoryFactory::class,
                    EmitterStack::class => EmitterFactory::class,
                    ErrorResponseGenerator::class => ServerRequestErrorResponseGeneratorFactory::class,
                    StreamInterface::class => StreamFactory::class,
                    ResponseInterface::class => ResponseFactory::class,
                ],
            ],
        ];
    }
}
