<?php

namespace Nano\Http\Contracts;

use Psr\Http\Message\ResponseInterface;
use Stringable;

interface JsonResponseInterface extends ResponseInterface, Stringable
{
    public function toString():string;
    public function setResponse(array $attributes):self;
    public function addToResponse(mixed $value): self;
}
