<?php

declare(strict_types=1);

namespace Antidot\Framework\Server;

use Antidot\Framework\Application;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Promise\PromiseInterface;
use Throwable;
use function React\Async\async;

final class ReactServerFactory
{
    public function __invoke(ContainerInterface $container): HttpServer
    {
        /** @var Application $application */
        $application = $container->get(Application::class);
        /** @var array{debug: bool} $globalConfig */
        $globalConfig = $container->get('config');

        $http = new HttpServer(
            function (ServerRequestInterface $request) use ($application): PromiseInterface {
                $_SERVER['X-Application'] = 'antidot-react-http';

                return async(static fn(): ResponseInterface => $application->handle($request));
            }
        );

        $http->on('error', function (Throwable $e) use ($globalConfig): void {
            if (true === $globalConfig['debug']) {
                dump($e);
                return;
            }

            var_export($e);
        });


        return $http;
    }
}
