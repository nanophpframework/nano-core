<?php

namespace Nano\Http;

use Closure;
use Nano\DependencyInjection\Contracts\ContainerInterface;
use Nano\Exceptions\InvalidResponseException;
use Nano\Http\Contracts\JsonResponseInterface;
use Nano\Http\Contracts\RouterInterface;
use Nano\Http\Enums\HttpStatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use ReflectionFunction;
use ReflectionNamedType;
use RuntimeException;

class RequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ContainerInterface $container,
    )
    {
        //
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $resolvedPath = $this->router->getAction($request->getUri()->getPath(), $request->getMethod());

        $action = $resolvedPath->action;
        $pathParams = $resolvedPath->params;
        $method = $resolvedPath->method;

        if (is_closure($action)) {
            return $this->resolveCallbackAction($action, $request, $pathParams);
        }

        if (class_exists($action)) {
            return $this->resolveActionClass($action, $pathParams, $request, $method);
        }

        throw new RuntimeException("Invalid action.");
    }

    private function resolveActionClass(string $action, array $pathParams, ServerRequestInterface $request, ?string $method = null): ResponseInterface
    {
        $reflection = new ReflectionClass($action);
        $parameters = [];
        if (method_exists($action, '__invoke') && is_null($method)) {
            $reflectionMethod = $reflection->getMethod('__invoke');

            $methodParameters = $reflectionMethod->getParameters();
            if (count($methodParameters) === 1 && $methodParameters[0]->getType() instanceof ReflectionNamedType && $methodParameters[0]->getType()->getName() === $request::class) {
                $parameters[] = $request;
            } else {
                foreach ($pathParams as &$parameter) {
                    $parameter = is_numeric($parameter) ? (int) $parameter : $parameter;
                    $parameters[] = $parameter;
                }
            }

            $instance = $this->container->get($action);
            $result = $instance($parameters);

            if (is_null($result)) {
                return response()->json();
            } else if ($result instanceof ResponseInterface) {
                return $result;
            } else if (is_array($result)) {
                return response()->json($result, HttpStatusCode::OK);
            }

            $instance = JsonResponseInterface::class;
            throw new InvalidResponseException("Response must be null or instance of {$instance}");
        } else if ($method){
            $reflectionMethod = $reflection->getMethod($method);
            $methodParameters = $reflectionMethod->getParameters();
            if (count($methodParameters) === 1 && $methodParameters[0]->getType() instanceof ReflectionNamedType && $methodParameters[0]->getType()->getName() === $request::class) {
                $parameters[] = $request;
            } else {
                foreach ($pathParams as &$parameter) {
                    $parameter = is_numeric($parameter) ? (int) $parameter : $parameter;
                    $parameters[] = $parameter;
                }
            }
        }

        $result = call_user_func_array([$this->container->get($action), $method], $parameters);

        if (is_null($result)) {
            return response()->json();
        } else if ($result instanceof ResponseInterface) {
            return $result;
        } else if (is_array($result)) {
            return response()->json($result, HttpStatusCode::OK);
        }

        $instance = JsonResponseInterface::class;
        throw new InvalidResponseException("Response must be null or instance of {$instance}");
    }

    private function resolveCallbackAction(Closure|string $action, ServerRequestInterface $request, array $pathParams): ResponseInterface
    {
        $reflection = new ReflectionFunction($action);

        $params = $reflection->getParameters();

        $parameters = [];
        if (count($params) === 1 && $params[0]->getType() instanceof ReflectionNamedType && $params[0]->getType()->getName() === $request::class) {
            $parameters[] = $request;
        } else {
            foreach ($pathParams as &$parameter) {
                if (is_numeric($parameter)) {
                    $parameter = (int) $parameter;
                }

                $parameters[] = $parameter;
            }
        }

        $result = call_user_func_array($action, $parameters);

        if (is_null($result)) {
            return response()->json();
        } else if ($result instanceof ResponseInterface) {
            return $result;
        } else if (is_array($result)) {
            return response()->json($result, HttpStatusCode::OK);
        }

        $instance = JsonResponseInterface::class;
        throw new InvalidResponseException("Response must be null or instance of {$instance}");
    }
}
