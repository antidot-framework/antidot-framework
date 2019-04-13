<?php

declare(strict_types=1);

namespace AntidotTest\Application\Http\Response;

use Antidot\Application\Http\Response\ServerRequestErrorResponseGenerator;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ServerRequestErrorResponseGeneratorTest extends TestCase
{
    /** @var Exception */
    private $throwable;
    /** @var ResponseInterface */
    private $response;

    public function testItShouldGenerateDefaultErrorResponse(): void
    {
        $this->givenAThrowableError();
        $this->whenExceptionIsInvoked();
        $this->thenResponseShouldHaveExpectedMessageAndStatusCode();
    }

    private function givenAThrowableError(): void
    {
        $this->throwable = new Exception('Test Exception', 500);
    }

    private function whenExceptionIsInvoked(): void
    {
        $errorResponseGenerator = new ServerRequestErrorResponseGenerator();
        $this->response = $errorResponseGenerator->__invoke($this->throwable);
    }

    private function thenResponseShouldHaveExpectedMessageAndStatusCode(): void
    {
        $this->assertEquals(500, $this->response->getStatusCode());
        $this->assertEquals(
            ServerRequestErrorResponseGenerator::ERROR_MESSAGE,
            (string)$this->response->getBody()
        );
    }
}
