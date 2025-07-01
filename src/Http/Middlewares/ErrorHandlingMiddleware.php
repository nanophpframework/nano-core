<?php

namespace Nano\Http\Middlewares;

use Nano\Exceptions\RenderableException;
use Nano\Http\Enums\HttpStatusCode;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorHandlingMiddleware extends AbstractMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->next($request, $handler);
        } catch(Throwable $e) {
            if ($e instanceof RenderableException) {
                return $e->render();
            } else if (env('APP_ENV') !== 'production') {
                $data = [
                    "message" => $e->getMessage(),
                    "file" => $e->getFile(),
                    "line" => $e->getLine(),
                    "trace" => $e->getTrace(),
                ];

                return response()->json($data, HttpStatusCode::INTERNAL_SERVER_ERROR);
            }

            return response()->json(["message" => "Internal Server Error"], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
