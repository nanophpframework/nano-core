<?php

namespace Nano\Http\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface RequestInterface extends ServerRequestInterface
{
    public static function capture(): self;
}
