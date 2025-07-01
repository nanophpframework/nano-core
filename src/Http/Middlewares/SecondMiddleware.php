<?php

namespace Nano\Http\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class SecondMiddleware extends AbstractMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo "Middleware 2".PHP_EOL;

        return $this->next($request, $handler);
    }
}
