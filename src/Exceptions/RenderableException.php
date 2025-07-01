<?php

namespace Nano\Exceptions;

use Psr\Http\Message\ResponseInterface;
use Throwable;

interface RenderableException extends Throwable
{
    public function render(): ResponseInterface;
}
