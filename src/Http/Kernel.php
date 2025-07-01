<?php

namespace Nano\Http;

use Nano\DependencyInjection\Contracts\ContainerInterface;
use Nano\Http\Contracts\RequestInterface;
use Nano\Http\Contracts\RouterInterface;

class Kernel
{
    public function __construct(
        private RouterInterface $route,
        private ContainerInterface $container,
    )
    {
        //
    }

    public function handle(RequestInterface $request): static
    {
        //
        return $this;
    }
}
