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
        set_error_handler(static function ($errno, $errstr, $errfile, $errline) {
            if (! (error_reporting() & $errno)) {
                // Error is not in mask
                return;
            }
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        try {
            if ($this->debug && class_exists(WhoopsMiddleware::class)) {
                $whoopsMiddleware = new WhoopsMiddleware();
                return $whoopsMiddleware->process($request, $handler);
            }

            $response = $handler->handle($request);
            return $response;
        } catch (Throwable $e) {
        }

        restore_error_handler();

        if ($this->debug && class_exists(WhoopsRunner::class)) {
            $whoops = new WhoopsRunner();
            return $whoops->handle($e, $request);
        }

        return new TextResponse('Unexpected Server Error Occurred', 500);
    }
}
