<?php

namespace Nano\Foundation\Contracts;

use Nano\Http\Contracts\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface KernelInterface
{
    public function handle(RequestInterface $request): ResponseInterface;
    public function setMiddlewares(array $middlewares): static;
}
