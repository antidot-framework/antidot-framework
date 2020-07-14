<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

use Psr\Http\Server\MiddlewareInterface;

interface Application
{
    public function run(): void;

    public function pipe(string $middlewareName): void;

    /**
     * @param array<MiddlewareInterface> $middleware
     */
    public function get(string $uri, array $middleware, string $name): void;

    /**
     * @param array<MiddlewareInterface> $middleware
     */
    public function post(string $uri, array $middleware, string $name): void;

    /**
     * @param array<MiddlewareInterface> $middleware
     */
    public function patch(string $uri, array $middleware, string $name): void;

    /**
     * @param array<MiddlewareInterface> $middleware
     */
    public function put(string $uri, array $middleware, string $name): void;

    /**
     * @param array<MiddlewareInterface> $middleware
     */
    public function delete(string $uri, array $middleware, string $name): void;

    /**
     * @param array<MiddlewareInterface> $middleware
     */
    public function options(string $uri, array $middleware, string $name): void;

    /**
     * @param array<MiddlewareInterface> $middleware
     */
    public function route(string $method, string $uri, array $middleware, string $name): void;
}
