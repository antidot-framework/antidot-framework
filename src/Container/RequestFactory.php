<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\ServerRequestFactory;

class RequestFactory
{
    public function __invoke(): callable
    {
        return static function (): RequestInterface {
            return ServerRequestFactory::fromGlobals(
                array_filter($_SERVER, 'is_string'),
                $_GET,
                $_POST,
                $_COOKIE,
                $_FILES
            );
        };
    }
}
