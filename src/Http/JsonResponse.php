<?php

namespace Nano\Http;

use Hugo\Psr7\Http\Response;
use Nano\Http\Contracts\JsonResponseInterface;
use Nano\Http\Enums\HttpStatusCode;
use Psr\Http\Message\StreamInterface;

class JsonResponse extends Response implements JsonResponseInterface
{
    public function __construct(
        private ?array $response = [],
        null|int|HttpStatusCode $statusCode = HttpStatusCode::OK,
        ?array $headers = [],
        string|StreamInterface|null $body = null,
        ?string $version = '1.1',
        ?string $reason = null
    )
    {
        parent::__construct(
            $this->resolveStatusCode($statusCode),
            $headers,
            $body,
            $version,
            $reason
        );
    }

    private function resolveStatusCode(null|int|HttpStatusCode $statusCode):int
    {
        if ($statusCode instanceof HttpStatusCode) {
            return $statusCode->value;
        } elseif (is_int($statusCode)) {
            return $statusCode;
        }

        return HttpStatusCode::OK->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return json_encode($this->response);
    }

    public function setResponse(array $attributes): JsonResponseInterface
    {
        $this->response = $attributes;
        $stream = $this->getBody();
        $stream->write(json_encode($attributes));
        $stream->rewind();
        return $this;
    }

    public function addToResponse(mixed $value): JsonResponseInterface
    {
        $this->response[] = $value;
        return $this;
    }
}
