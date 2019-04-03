<?php

declare(strict_types=1);

namespace Antidot\Infrastructure\Aura\Router;

use Antidot\Application\Http\Route;

class AuraRoute implements Route
{
    /** @var array */
    private $method;
    /** @var string */
    private $name;
    /** @var string */
    private $path;
    /** @var array */
    private $pipeline;

    public function __construct(array $method, string $name, string $path, array $pipeline)
    {
        $this->method = $method;
        $this->name = $name;
        $this->path = $path;
        $this->pipeline = $pipeline;
    }

    public function pipeline(): array
    {
        return $this->pipeline;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function method(): array
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }
}
