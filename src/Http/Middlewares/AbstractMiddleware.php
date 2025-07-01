<?php

namespace Nano\Http\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    public function __construct(protected ?MiddlewareInterface $next = null)
    {
        //
    }

    protected function next(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->next) {
            return $this->next->process($request, $handler);
        }

        return $handler->handle($request);
    }

    public function setNext(MiddlewareInterface $next): static
    {
        $this->next = $next;
        return $this;
    }
}
