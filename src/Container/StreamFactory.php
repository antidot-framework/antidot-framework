<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

class StreamFactory
{
    public function __invoke(): callable
    {
        return static function (): StreamInterface {
            return new Stream('php://temp', 'wb+');
        };
    }
}
