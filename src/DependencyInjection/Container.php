<?php

namespace Nano\DependencyInjection;

use HugoAndrade\ContainerDI\Container\DI;
use Nano\DependencyInjection\Contracts\ContainerInterface;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

class Container extends DI implements ContainerInterface
{
    public function bind(string $abastract, mixed $concrete): void
    {
        $this->set($abastract, $concrete);
    }

    public function make(string $abastract): mixed
    {
        return $this->get($abastract);
    }

    public function getMethodParameters(object $class, string $method): mixed
    {
        $stringMethod = $class::class . "::$method";
        $reflection = ReflectionMethod::createFromMethodName("{$stringMethod}");

        $params = $reflection->getParameters();

        if (count($params) > 0) {
            $parameters = [];
            foreach ($params as $param) {
                $type = $param->getType();
                $name = $param->getName();
                if ($type instanceof ReflectionNamedType) {
                    if ($this->has($type->getName())) {
                        $instance = $this->get($type->getName());
                        $parameters[$name] = $instance;
                    } else {
                        if ($type->getName() == 'int' || $type->getName() == 'string') {
                            $parameters[$name] = $type->getName();
                        } else {
                            throw new RuntimeException("Can't resolve parameters");
                        }
                    }
                }
            }

            return $parameters;
        }
        return [];
    }

    public static function getInstance(): static
    {
        return new self;
    }
}
