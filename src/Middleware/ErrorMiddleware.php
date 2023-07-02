<?php

declare(strict_types=1);

namespace Antidot\Framework\Middleware;

use Antidot\Framework\Exception\WhoopsRunner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RingCentral\Psr7\Response;
use Throwable;

use function class_exists;

final class ErrorMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly bool $debug = true
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $exception) {
            return $this->getErrorResponse($exception, $request);
        }
    }

    private function getErrorResponse(Throwable $exception, ServerRequestInterface $request): ResponseInterface
    {
        if (true === $this->debug && class_exists(\Whoops\Run::class)) {
            $whoops = new WhoopsRunner();

            return $whoops::handle($exception, $request);
        }

        if (true === $this->debug) {
            $previousExceptions = [];
            $previous = $exception->getPrevious();
            while ($previous) {
                $previousExceptions[] = [
                    'message' => $previous->getMessage(),
                    'file' => $previous->getFile(),
                    'line' => $previous->getLine(),
                ];
                $previous = $previous->getPrevious();
            }

            return new Response(500, ['Content-Type' => 'application/json'], json_encode([
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'previous' => $previousExceptions,
                'trace' => $exception->getTrace(),
            ]));
        }

        return new Response(500, [], 'Unexpected Server Error Occurred');
    }
}
