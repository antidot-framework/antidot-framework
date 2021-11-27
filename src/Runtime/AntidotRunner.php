<?php

declare(strict_types=1);

namespace Antidot\Framework\Runtime;

use Antidot\Framework\Application;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Promise\PromiseInterface;
use React\Socket\SocketServer;
use Symfony\Component\Runtime\RunnerInterface;
use Throwable;
use function React\Async\async;

final class AntidotRunner implements RunnerInterface
{
    public function __construct(
        private Application $application,
        private RunnerOptions $runnerOptions
    ) {
    }

    public function run(): int
    {
        if (PHP_SAPI === 'cli') {
            return $this->runAsync($this->application);
        }

        return $this->runSync($this->application);
    }

    private function runAsync(Application $application): int
    {
        $http = new HttpServer(
            function (ServerRequestInterface $request) use ($application): PromiseInterface {
                $_SERVER['X-Application'] = 'antidot-react-http';

                return async(static fn(): ResponseInterface => $application->handle($request));
            }
        );

        $http->on('error', function (Throwable $e): void {
            if (true === $this->runnerOptions->debug) {
                dump($e);
                return;
            }

            var_export($e);
        });

        $socket = new SocketServer(
            sprintf('%s:%d', $this->runnerOptions->host, $this->runnerOptions->port),
            ['tcp' => ['so_reuseport' => true]]
        );
        $http->listen($socket);

        return 0;
    }

    private function runSync(Application $application): int
    {
        $sapi = new SapiEmitter();
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator(
            $psr17Factory, // ServerRequestFactory
            $psr17Factory, // UriFactory
            $psr17Factory, // UploadedFileFactory
            $psr17Factory  // StreamFactory
        );

        $sapi->emit($application->handle($creator->fromGlobals()));

        return 0;
    }
}
