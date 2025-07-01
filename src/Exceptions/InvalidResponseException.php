<?php

namespace Nano\Exceptions;

use Exception;
use Nano\Http\Enums\HttpStatusCode;
use Psr\Http\Message\ResponseInterface;

class InvalidResponseException extends Exception implements RenderableException
{
    public function render(): ResponseInterface
    {
        $data = [
            "message" => $this->getMessage(),
            "file" => $this->getFile(),
            "line" => $this->getLine(),
            "trace" => $this->getTrace(),
        ];

        return response()->json($data, HttpStatusCode::INTERNAL_SERVER_ERROR);
    }
}
