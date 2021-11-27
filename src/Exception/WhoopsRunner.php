<?php

namespace Antidot\Framework\Exception;

use Antidot\Framework\Application;
use Franzl\Middleware\Whoops\FormatNegotiator;
use Franzl\Middleware\Whoops\Formats\Format;
use Middlewares\Utils\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class WhoopsRunner
{
    /** @psalm-suppress ReservedWord */
    public static function handle(mixed $error, ServerRequestInterface $request): ResponseInterface
    {
        $method = Run::EXCEPTION_HANDLER;

        $format = FormatNegotiator::negotiate($request);
        $whoops = self::getWhoopsInstance($format);

        // Output is managed by the middleware pipeline
        $whoops->allowQuit(false);

        ob_start();
        $whoops->$method($error);
        $content = ob_get_clean();

        /** @var string|array<string> $contentType */
        $contentType = $format->getPreferredContentType();

        return Factory::createResponse(500)
            ->withBody(Factory::createStream($content ?: ''))
            ->withHeader('Content-Type', $contentType);
    }

    private static function getWhoopsInstance(Format $format): Run
    {
        $whoops = new Run();
        if (Application::NAME === ($_SERVER['X-Application'] ?? '')) {
            $handler = new PrettyPageHandler();
            $handler->handleUnconditionally(true);
            $whoops->pushHandler($handler);
            return $whoops;
        }

        $whoops->pushHandler($format->getHandler());
        return $whoops;
    }
}
