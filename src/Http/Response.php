<?php

namespace Nano\Http;

use Hugo\Psr7\Http\Response as HttpResponse;
use Nano\Http\Contracts\JsonResponseInterface;
use Nano\Http\Contracts\ResponseInterface;
use Nano\Http\Enums\HttpStatusCode;
use Psr\Http\Message\StreamInterface;

class Response extends HttpResponse implements ResponseInterface
{
    
    public function __construct(
        private ?array $response = [],
        null|int|HttpStatusCode $statusCode = HttpStatusCode::OK,
        ?array $headers = [],
        string|StreamInterface|null $body = null,
        ?string $version = '1.1',
        ?string $reason = null,
        private ?JsonResponseInterface $jsonResponse = null,
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

    public function json(?array $attributes = null, null|int|HttpStatusCode $statusCode = null): JsonResponseInterface
    {
        if (is_null($this->jsonResponse)) {
            $this->jsonResponse = app(JsonResponseInterface::class);
        }

        $this->jsonResponse->setResponse($attributes ?? $this->response);
        $this->jsonResponse = $this->jsonResponse->withStatus($this->resolveStatusCode($statusCode ?? $this->getStatusCode()));
        $this->jsonResponse = $this->jsonResponse->withHeader("content-type", ["application/json", "charset=utf-8"]);
        return $this->jsonResponse;
    }

    private function resolveStatusCode(null|int|HttpStatusCode $statusCode = null):int
    {
        if ($statusCode instanceof HttpStatusCode) {
            return $statusCode->value;
        } elseif (is_int($statusCode)) {
            return $statusCode;
        }

        return HttpStatusCode::OK->value;
    }
}
