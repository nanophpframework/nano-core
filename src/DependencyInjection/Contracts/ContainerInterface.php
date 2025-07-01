<?php

namespace Nano\DependencyInjection\Contracts;

use Psr\Container\ContainerInterface as Psr7ContainerInterface;

interface ContainerInterface extends Psr7ContainerInterface
{
    public function bind(string $abastract, mixed $concrete): void;
    public function make(string $abastract): mixed;
    public function getMethodParameters(object $class, string $method): mixed;
}
