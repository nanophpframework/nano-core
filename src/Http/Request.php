<?php

namespace Nano\Http;

use Hugo\Psr7\Http\ServerRequest;
use Hugo\Psr7\Http\UploadedFile;
use Nano\Http\Contracts\RequestInterface;

class Request extends ServerRequest implements RequestInterface
{
    public static function capture(): self
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $newKey = str_replace('HTTP_', '', $key);
                $headers[$newKey] = $value;
            }
        }

        $uploads = [];
        foreach ($_FILES as $file) {
            $uploads[] = new UploadedFile(
                $file['tmp_name'],
                $file['size'],
                $file['error'],
                $file['name'],
                $file['type']
            );
        }

        return new self(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            $headers,
            file_get_contents('php://input'),
            $_SERVER['SERVER_PROTOCOL'],
            $_SERVER,
            $uploads,
            $_COOKIE
        );
    }
}
