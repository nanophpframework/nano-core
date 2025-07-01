<?php

namespace Nano\Http\Router;

use Closure;
use Nano\Exceptions\RoutingException;
use Nano\Http\Contracts\RouterInterface;
use Nano\Http\Enums\HttpStatusCode;
use RuntimeException;

class Route implements RouterInterface
{
    protected static array $action = [];
    protected static array $separated = [];

    public static function post(string $path, array|string|Closure $action): void
    {
        if (mb_strlen($path) > 1) 
            $path = trim($path, "\n\r\t\v\x00\/");

        $method = 'POST';
        if (is_array($action)) {
            if(!class_exists($action[0])) throw new RuntimeException("Class '{$action[0]}' not found.");
            self::$action[$path][$method] = implode('@', $action);
        }

        if (is_string($action) || is_closure($action))
            self::$action[$path][$method] = $action;

        self::$separated[$method][] = explode('/', $path);
    }

    public static function put(string $path, array|string|Closure $action): void
    {
        if (mb_strlen($path) > 1) 
            $path = trim($path, "\n\r\t\v\x00\/");

        $method = 'PUT';
        if (is_array($action)) {
            if(!class_exists($action[0])) throw new RuntimeException("Class '{$action[0]}' not found.");
            self::$action[$path][$method] = implode('@', $action);
        }

        if (is_string($action) || is_closure($action))
            self::$action[$path][$method] = $action;

        self::$separated[$method][] = explode('/', $path);
    }

    public static function patch(string $path, array|string|Closure $action): void
    {
        if (mb_strlen($path) > 1) 
            $path = trim($path, "\n\r\t\v\x00\/");

        $method = 'PATCH';
        if (is_array($action)) {
            if(!class_exists($action[0])) throw new RuntimeException("Class '{$action[0]}' not found.");
            self::$action[$path][$method] = implode('@', $action);
        }

        if (is_string($action) || is_closure($action))
            self::$action[$path][$method] = $action;

        self::$separated[$method][] = explode('/', $path);
    }

    public static function get(string $path, array|string|Closure $action): void
    {
        if (mb_strlen($path) > 1) 
            $path = trim($path, "\n\r\t\v\x00\/");

        $method = 'GET';
        if (is_array($action)) {
            if(!class_exists($action[0])) throw new RuntimeException("Class '{$action[0]}' not found.");
            self::$action[$path][$method] = implode('@', $action);
        }

        if (is_string($action) || is_closure($action))
            self::$action[$path][$method] = $action;

        self::$separated[$method][] = explode('/', $path);
    }

    public static function delete(string $path, array|string|Closure $action): void
    {
        if (mb_strlen($path) > 1) 
            $path = trim($path, "\n\r\t\v\x00\/");

        $method = 'DELETE';
        if (is_array($action)) {
            if(!class_exists($action[0])) throw new RuntimeException("Class '{$action[0]}' not found.");
            self::$action[$path][$method] = implode('@', $action);
        }

        if (is_string($action) || is_closure($action))
            self::$action[$path][$method] = $action;

        self::$separated[$method][] = explode('/', $path);
    }

    public function getAction(string $path, string $method): ResolvedPath
    {
        $machedRoute = $this->resolve($path, $method);

        if (!isset(self::$action[$machedRoute->getStringPath()])) throw new RoutingException("Path not found", HttpStatusCode::NOT_FOUND->value);
        if (!isset(self::$action[$machedRoute->getStringPath()][$method])) throw new RoutingException("Method not allowed", HttpStatusCode::METHOD_NOT_ALLOWED->value);

        $action = self::$action[$machedRoute->getStringPath()][$method];

        $separated = [];
        if (is_string($action) && str_contains($action, '@')) {
            $separated = explode('@', $action);
        }

        return new ResolvedPath(
            $machedRoute->getStringPath(),
            $machedRoute->params,
            is_string($action) && str_contains($action, '@') ? $separated[0] : $action,
            is_string($action) && str_contains($action, '@') ? $separated[1] : null
        );
    }

    private function resolve(string $path, string $method): MatchedRoute
    {
        if (mb_strlen($path) > 1) 
            $path = trim($path, "\n\r\t\v\x00\/");

        if (!isset(self::$separated[$method])) throw new RoutingException("Method not allowed", HttpStatusCode::METHOD_NOT_ALLOWED->value);

        $pathArray = explode('/', $path);
        $total = count($pathArray);

        $path = [];
        $routeParams = [];

        foreach ($pathArray as $key => $part) {
            $path = array_filter(self::$separated[$method], function($item) use ($key, $part, $total, &$routeParams) {
                if (count($item) !== $total) return false;
                if (!isset($item[$key])) return false;

                $regexResult = preg_match("/{(.*?)}/", $item[$key]);

                if ($item[$key] === $part) return true;
                if ($regexResult !== false && $regexResult === 1) {
                    $routeParams[] = $part;
                    return true;
                }

                return false;
            });
        }

        if (count($path) > 1) throw new RuntimeException("Ambiguous path defined.");
        return new MatchedRoute(array_shift($path) ?? [], $routeParams);
    }

    public function all(): array
    {
        return [
            self::$action,
            self::$separated,
        ];
    }
}
