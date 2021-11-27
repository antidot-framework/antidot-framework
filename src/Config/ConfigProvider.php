<?php

declare(strict_types=1);

namespace Antidot\Framework\Config;

use Antidot\Framework\Cli\ServeCommand;
use Antidot\Framework\Cli\ServeCommandFactory;
use Antidot\Framework\Middleware\ErrorMiddleware;
use Antidot\Framework\Middleware\MiddlewareFactory;
use Antidot\Framework\Middleware\RouteDispatcherMiddleware;
use Antidot\Framework\Middleware\RouteNotFoundMiddleware;
use Antidot\Framework\Router\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class ConfigProvider
{
    /**
     * @return array<mixed>
     */
    public function __invoke(): array
    {
        return [
            'console' => [
                'factories' => [
                    ServeCommand::class => ServeCommandFactory::class,
                ],
                'commands' => [
                    ServeCommand::NAME => ServeCommand::class,
                ],
            ],
            'server' => [
                'workers' => 4
            ],
            'dependencies' => [
                'services' => [
                    RouteDispatcherMiddleware::class => RouteDispatcherMiddleware::class,
                    RouteNotFoundMiddleware::class => RouteNotFoundMiddleware::class,
                    ErrorMiddleware::class => ErrorMiddleware::class,
                    RequestFactoryInterface::class => Psr17Factory::class,
                    ResponseFactoryInterface::class => Psr17Factory::class,
                ],
                'factories' => [
                    Router::class => FastRouterFactory::class,
                    MiddlewareFactory::class => MiddlewareFactoryFactory::class,
                ],
            ],
        ];
    }
}
