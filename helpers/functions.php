<?php

use Nano\DependencyInjection\Container;
use Nano\Http\Contracts\ResponseInterface;
use Nano\Http\Enums\HttpStatusCode;
use Nano\Http\JsonResponse;
use Nano\Http\Response;

if (!function_exists('is_closure')) {
    function is_closure($var): bool
    {
        return $var instanceof Closure || is_callable($var);
    }
}

if (!function_exists('response')) {
    function response(array $atributes =[], null|int|HttpStatusCode $statusCode = HttpStatusCode::OK): ResponseInterface
    {
        return new Response($atributes, $statusCode, jsonResponse: new JsonResponse());
    }
}

if (!function_exists('env')) {
    function env(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);
        if (!$value) 
            return $default;

        return $value;
    }
}

if (!function_exists('app')) {
    function app(?string $abstract = null): mixed {
        $container = Container::getInstance();
        if (is_null($abstract))
            return $container;

        return $container->get($abstract);
    }
}