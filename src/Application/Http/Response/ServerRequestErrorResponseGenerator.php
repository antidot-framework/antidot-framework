<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Response;

use Psr\Http\Message\ResponseInterface;
use Throwable;
use Zend\Diactoros\Response;

final class ServerRequestErrorResponseGenerator implements ErrorResponseGenerator
{
    public const ERROR_MESSAGE = 'An unexpected error occurred';
    public const ERROR_CODE = 500;

    public function __invoke(Throwable $e): ResponseInterface
    {
        $response = new Response();
        $response = $response->withStatus($this->getStatusCode($e, $response));

        $response->getBody()->write(self::ERROR_MESSAGE);

        return $response;
    }

    private function getStatusCode(Throwable $exception, ResponseInterface $response): int
    {
        $statusCode = $exception->getCode() ?? $response->getStatusCode() ?? self::ERROR_CODE;
        if (is_string($statusCode)) {
            $statusCode = self::ERROR_CODE;
        }

        return $statusCode;
    }
}
