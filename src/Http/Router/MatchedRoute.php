<?php

namespace Nano\Http\Router;

use Stringable;

readonly class MatchedRoute implements Stringable
{
    public function __construct(
        private array $path,
        public array $params,
    )
    {
        //
    }

    public function __toString(): string
    {
        return $this->getStringPath();
    }

    public function getStringPath(): string
    {
        return implode('/', $this->path);
    }
}
