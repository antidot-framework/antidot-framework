<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Middleware;

use ErrorException;
use Franzl\Middleware\Whoops\WhoopsMiddleware;
use Franzl\Middleware\Whoops\WhoopsRunner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Zend\Diactoros\Response\TextResponse;

use function class_exists;
use function error_reporting;
use function restore_error_handler;
use function set_error_handler;

class ErrorMiddleware implements MiddlewareInterface
{
    /** @var bool */
    private $debug;

    public function __construct(bool $debug)
    {
        $this->debug = $debug;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->setErrorHandler();

        try {
            if ($this->debug && class_exists(WhoopsMiddleware::class)) {
                $whoopsMiddleware = new WhoopsMiddleware();
                $response = $whoopsMiddleware->process($request, $handler);
                restore_error_handler();
                return $response;
            }

            $response = $handler->handle($request);
            restore_error_handler();
            return $response;
        } catch (Throwable $exception) {
            restore_error_handler();
            return $this->getErrorResponse($exception, $request);
        }
    }

    private function setErrorHandler(): void
    {
        $handler = static function (
            int $errorNumber,
            string $errorString,
            string $errorFile,
            int $errorLine,
            ?array $errorContext
        ): bool {
            if (!(error_reporting() & $errorNumber)) {
                // Error is not in mask
                return false;
            }
            throw new ErrorException($errorString, 0, $errorNumber, $errorFile, $errorLine);
        };

        set_error_handler($handler);
    }

    private function getErrorResponse(Throwable $exeption, ServerRequestInterface $request): ResponseInterface
    {
        if ($this->debug && class_exists(WhoopsRunner::class)) {
            $whoops = new WhoopsRunner();
            return $whoops->handle($exeption, $request);
        }

        return new TextResponse('Unexpected Server Error Occurred', 500);
    }
}
