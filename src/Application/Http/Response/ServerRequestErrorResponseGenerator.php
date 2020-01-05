<?php

declare(strict_types=1);

namespace Antidot\Application\Http\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Laminas\Diactoros\Response;

final class ServerRequestErrorResponseGenerator implements ErrorResponseGenerator
{
    public const ERROR_MESSAGE = 'An unexpected error occurred';
    public const ERROR_CODE = 500;
    /** @var bool * */
    private $devMode;

    public function __construct(bool $devMode = false)
    {
        $this->devMode = $devMode;
    }

    public function __invoke(
        Throwable $e,
        ServerRequestInterface $request = null,
        ResponseInterface $response = null
    ): ResponseInterface {
        if (null === $response) {
            $response = new Response();
        }
        $response = $response->withStatus(self::ERROR_CODE);

        if ($this->devMode) {
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write($this->getErrorAsJsonString($e, $request));

            return $response;
        }

        $response->getBody()->write(self::ERROR_MESSAGE);

        return $response;
    }

    private function getErrorAsJsonString(Throwable $e, ServerRequestInterface $request = null): string
    {
        $responseString = json_encode([
            'exception' => [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),

                'previous' => null === $e->getPrevious() ?: get_class($e->getPrevious()),
                'trace' => $e->getTrace(),
            ],
            'request' => null === $request ?: [
                'headers' => $request->getHeaders(),
            ]
        ]);

        return is_string($responseString) ? $responseString : $e->getMessage();
    }
}
