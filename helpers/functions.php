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

if (!function_exists('base_path')) {
    /**
     *  Return path to the root of project
     */
    function base_path(): string
    {
        return realpath(__DIR__."/../");
    }
}

if (!function_exists("routes_path")) {
    function routes_path(): string
    {
        return base_path()."/routes";
    }
}

if (!function_exists('config_path')) {
    function config_path(): string
    {
        return base_path()."/config";
    }
}