<?php

declare(strict_types=1);

namespace Antidot\Container\Config;

use Antidot\Application\Http\Application;
use Antidot\Application\Http\Middleware\ErrorMiddleware;
use Antidot\Application\Http\Middleware\RouteDispatcherMiddleware;
use Antidot\Application\Http\Middleware\RouteNotFoundMiddleware;
use Antidot\Application\Http\Response\ErrorResponseGenerator;
use Antidot\Application\Http\Response\ServerRequestErrorResponseGenerator;
use Antidot\Container\ApplicationFactory;
use Antidot\Container\EmitterFactory;
use Antidot\Container\ErrorMiddlewareFactory;
use Antidot\Container\MiddlewareFactory;
use Antidot\Container\MiddlewareFactoryFactory;
use Antidot\Container\RequestFactory;
use Antidot\Container\RequestHandlerFactory;
use Antidot\Container\RequestHandlerFactoryFactory;
use Antidot\Container\ResponseFactory;
use Antidot\Container\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterStack;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'services' => [
                RouteDispatcherMiddleware::class => RouteDispatcherMiddleware::class,
                RouteNotFoundMiddleware::class => RouteNotFoundMiddleware::class,
                ErrorResponseGenerator::class => ServerRequestErrorResponseGenerator::class,
                RequestFactory::class => RequestFactory::class,
            ],
            'factories' => [
                Application::class => ApplicationFactory::class,
                MiddlewareFactory::class => MiddlewareFactoryFactory::class,
                RequestHandlerFactory::class => RequestHandlerFactoryFactory::class,
                EmitterStack::class => EmitterFactory::class,
                ErrorMiddleware::class => ErrorMiddlewareFactory::class,
                StreamInterface::class => StreamFactory::class,
                ResponseInterface::class => ResponseFactory::class,
            ],
        ];
    }
}
