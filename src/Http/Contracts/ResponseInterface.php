<?php

namespace Nano\Http\Contracts;

use Nano\Http\Enums\HttpStatusCode;
use Psr\Http\Message\ResponseInterface as MessageResponseInterface;

interface ResponseInterface extends MessageResponseInterface
{
    public function json(?array $attributes = null, null|int|HttpStatusCode $statusCode = HttpStatusCode::OK): JsonResponseInterface;
}
