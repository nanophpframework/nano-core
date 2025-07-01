<?php

namespace Nano\Http\Contracts;

use Psr\Http\Message\RequestInterface as MessageRequestInterface;

interface RequestInterface extends MessageRequestInterface
{
    public static function capture(): self;
}
