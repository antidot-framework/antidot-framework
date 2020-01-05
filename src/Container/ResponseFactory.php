<?php

declare(strict_types=1);

namespace Antidot\Container;

use Laminas\Diactoros\Response;

class ResponseFactory
{
    public function __invoke(): callable
    {
        return static function () : Response {
            return new Response();
        };
    }
}
