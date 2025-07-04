<?php

namespace Nano\Foundation;

use Nano\DependencyInjection\Container;
use Nano\DependencyInjection\Contracts\ContainerInterface;
use Nano\Foundation\Contracts\KernelInterface;
use Nano\Foundation\Contracts\ProviderInterface;
use Nano\Http\Contracts\RequestInterface;
use Nano\Http\Contracts\RouterInterface;
use Nano\Http\Kernel;
use Nano\Http\Router\Route;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Application
{
    private array $providers = [];

    public function __construct(
        private ContainerInterface $container,
        private ?string $basePath = null,
    )
    {
        if (is_null($basePath)) $this->basePath = base_path();
        $this->container->bind(KernelInterface::class, Kernel::class);
    }

    public static function create(?string $basePath = null): static
    {
        $container = new Container;
        $container->bind(ContainerInterface::class, Container::class);
        $container->bind(PsrContainerInterface::class, Container::class);
        return new self($container, $basePath);
    }

    public function handleRequest(RequestInterface $request)
    {
        $this->initBuffer();
        $this->withProviders([
            RouteServiceProvider::class
        ]);
        $this->container->bind(RequestInterface::class, $request);

        /** @var KernelInterface */
        $kernel = $this->container->get(KernelInterface::class);
        $response = $kernel->handle($request);

        $this->sendResponse($response);
    }

    public function withRouting(string $path): static
    {
        $this->register($path);
        return $this;
    }

    private function register(string $path)
    {
        if (!file_exists($path)) throw new RuntimeException("Route path '{$path}' not found");
        require $path;
    }

    protected function getConfig(): array
    {
        return require config_path()."/app.php";
    }

    public function withProviders(?array $providers = []): static
    {
        foreach ($providers as $provider) {
            if (!class_exists($provider)) {
                throw new RuntimeException("Provider '{$provider} not found.'");
            }
            /** @var \Nano\Foundation\Contracts\ProviderInterface */
            $instance = $this->container->get($provider);
            if (!$instance instanceof ProviderInterface) {
                $interface = ProviderInterface::class;
                throw new RuntimeException("Provider '{$provider}' must be instance of {$interface}");
            }
            $instance->register();
            $this->providers[] = $instance;
        }

        return $this;
    }

    private function initBuffer(): bool
    {
        return ob_start();
    }

    private function sendResponse(ResponseInterface $response): void
    {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $header = "{$name}: $value";
            header($header);
        }

        $body = $response->getBody()->getContents();
        file_put_contents('php://output', $body);
        $bufferContent = ob_get_contents();

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $bufferContent);
        rewind($stream);

        $contentType = mime_content_type($stream);
        fclose($stream);

        if ($contentType === 'application/javascript') {
            $contentType = "text/html";
        }

        header("Content-Type: {$contentType}");
        $this->flushBuffer();
    }

    private function flushBuffer(): bool
    {
        return ob_flush();
    }
}
