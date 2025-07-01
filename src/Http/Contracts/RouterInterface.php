<?php

namespace Nano\Http\Contracts;

use Closure;
use Nano\Http\Router\ResolvedPath;

interface RouterInterface
{
    public static function post(string $path, array|string|Closure $action): void;
    public static function put(string $path, array|string|Closure $action): void;
    public static function patch(string $path, array|string|Closure $action): void;
    public static function get(string $path, array|string|Closure $action): void;
    public static function delete(string $path, array|string|Closure $action): void;
    public function getAction(string $path, string $method): ResolvedPath;
    public function all(): array;
}
