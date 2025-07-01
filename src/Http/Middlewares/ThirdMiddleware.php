<?php

namespace Nano\Http\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class ThirdMiddleware extends AbstractMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo "Third middleware" . PHP_EOL;
        return $this->next($request, $handler);
    }
}
