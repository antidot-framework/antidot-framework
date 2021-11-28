<?php

declare(strict_types=1);

namespace Antidot\Framework\Config;

use Antidot\Framework\Application;
use Antidot\Framework\Cli\ServeCommand;
use Antidot\Framework\Cli\ServeCommandFactory;
use Antidot\Framework\Middleware\ErrorMiddleware;
use Antidot\Framework\Middleware\MiddlewareFactory;
use Antidot\Framework\Middleware\RouteDispatcherMiddleware;
use Antidot\Framework\Middleware\RouteNotFoundMiddleware;
use Antidot\Framework\Router\Router;
use Antidot\Framework\Server\ReactServerFactory;
use Antidot\Framework\Server\ReactSocketFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use React\Http\HttpServer;
use React\Socket\SocketServer;

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
                'workers' => 4,
                'host' => '0.0.0.0',
                'port' => 3000,
            ],
            'dependencies' => [
                'invokables' => [
                    RouteDispatcherMiddleware::class => RouteDispatcherMiddleware::class,
                    RouteNotFoundMiddleware::class => RouteNotFoundMiddleware::class,
                    ErrorMiddleware::class => ErrorMiddleware::class,
                    RequestFactoryInterface::class => Psr17Factory::class,
                    ResponseFactoryInterface::class => Psr17Factory::class,
                ],
                'factories' => [
                    Application::class => ApplicationFactory::class,
                    Router::class => FastRouterFactory::class,
                    MiddlewareFactory::class => MiddlewareFactoryFactory::class,
                    HttpServer::class => ReactServerFactory::class,
                    SocketServer::class => ReactSocketFactory::class,
                ],
            ],
        ];
    }
}
