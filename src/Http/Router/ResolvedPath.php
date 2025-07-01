<?php

namespace Nano\Http\Router;

use Closure;

readonly class ResolvedPath
{
    public function __construct(
        public string $path,
        public array $params,
        public string|Closure $action,
        public ?string $method = null
    )
    {
        //
    }
}
