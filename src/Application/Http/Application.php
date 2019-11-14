<?php

declare(strict_types=1);

namespace Antidot\Application\Http;

interface Application
{
    public function run(): void;
    public function pipe(string $middlewareName): void;
    public function get(string $uri, array $middleware, string $name): void;
    public function post(string $uri, array $middleware, string $name): void;
    public function patch(string $uri, array $middleware, string $name): void;
    public function put(string $uri, array $middleware, string $name): void;
    public function delete(string $uri, array $middleware, string $name): void;
    public function options(string $uri, array $middleware, string $name): void;
    public function route(string $method, string $uri, array $middleware, string $name): void;
}
