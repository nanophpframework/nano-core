<?php

namespace Nano\Http;

use Nano\DependencyInjection\Contracts\ContainerInterface;
use Nano\Foundation\Contracts\KernelInterface;
use Nano\Http\Contracts\RequestInterface;
use Nano\Http\Contracts\RouterInterface;
use Nano\Http\Middlewares\AbstractMiddleware;
use Nano\Http\Middlewares\ErrorHandlingMiddleware;
use Psr\Http\Message\ResponseInterface;

class Kernel implements KernelInterface
{
    /**
     * @var string[]
     */
    private array $middlewares = [];

    private AbstractMiddleware $middlewareChain;
    private RequestHandler $requestHandler;
    
    public function __construct(
        private RouterInterface $route,
        private ContainerInterface $container,
        array $middlewares = [],
    )
    {
        $this->middlewares = $middlewares;
        $this->middlewareChain = new ErrorHandlingMiddleware;
        $this->requestHandler = new RequestHandler(
            $this->route,
            $this->container
        );
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $this->initMiddlewares();
        return $this->dispatch($request);
    }

    private function dispatch(RequestInterface $request): ResponseInterface
    {
        return $this->middlewareChain->process($request, $this->requestHandler);
    }

    private function initMiddlewares()
    {
        $first = $this->middlewareChain;
        $last = $first;

        foreach ($this->middlewares as $class) {
            /** @var \Nano\Http\Middlewares\AbstractMiddleware */
            $instance = $this->container->get($class);
            $last->setNext($instance);

            $last = $instance;
        }

        $this->middlewareChain = $first;
    }

    public function setMiddlewares(array $middlewares): static
    {
        $this->middlewares = $middlewares;
        return $this;
    }
}
